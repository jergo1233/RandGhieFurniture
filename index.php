<?php
// ==========================================
// 1. Database Configuration
// ==========================================
// Tips: I-comment out ang hindi ginagamit.
$host = 'sql302.infinityfree.com';
$db   = 'if0_41839551_furniture_db';
$user = 'if0_41839551';
$pass = 'GinsU091277'; 
$charset = 'utf8mb4';

// $host = 'localhost';
// $db   = 'furniture_db';
// $user = 'root';
// $pass = ''; 
// $charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

// ==========================================
// 2. API ENDPOINTS (Dapat nasa itaas para sa AJAX)
// ==========================================
if (isset($_GET['api'])) {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    $api = $_GET['api'];
    $data = json_decode(file_get_contents('php://input'), true);

    // --- LOGIN API ---
    if ($api === 'login') {
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();
        echo json_encode(['success' => !!$user]);
    }

    // --- GET PRODUCTS ---
    elseif ($api === 'products') {
        try {
            $stmt = $pdo->query("SELECT id, name, price, category, description, image FROM products ORDER BY id DESC");
            $products = $stmt->fetchAll();
            foreach ($products as &$p) {
                if ($p['image']) {
                    $p['image'] = base64_encode($p['image']);
                }
            }
            echo json_encode($products);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // --- ADD PRODUCT API ---
   // elseif ($api === 'add_product') {
      //  try {
         //   $binary_images = [null, null, null];
           // foreach ($data['images'] as $index => $img_data) {
             //   if ($index >= 3) break;
              //  if (preg_match('/^data:image\/(\w+);base64,/', $img_data)) {
                //    $img_data = substr($img_data, strpos($img_data, ',') + 1);
               // }
            //    $binary_images[$index] = base64_decode($img_data);
          //  }
          //  $sql = "INSERT INTO products (name, price, category, description, image, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?)";
          //  $stmt = $pdo->prepare($sql);
        //    $stmt->execute([$data['name'], $data['price'], $data['category'], $data['description'], $binary_images[0], $binary_images[1], $binary_images[2]]);
          //  echo json_encode(['success' => true]);
      //  } catch (PDOException $e) {
      //      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
      //  }
   // }
    
    elseif ($api === 'add_product') {
    try {
        $binary_images = [null, null, null];
        
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $index => $img_data) {
                if ($index >= 3) break;
                if (!empty($img_data)) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $img_data)) {
                        $img_data = substr($img_data, strpos($img_data, ',') + 1);
                    }
                    $binary_images[$index] = base64_decode($img_data);
                }
            }
        }

        $sql = "INSERT INTO products (name, price, category, description, image, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // Explicit parameter binding para sa LOB (Large Objects) / Null values
        $stmt->bindValue(1, $data['name'] ?? '');
        $stmt->bindValue(2, $data['price'] ?? 0);
        $stmt->bindValue(3, $data['category'] ?? '');
        $stmt->bindValue(4, $data['description'] ?? '');

        for ($i = 0; $i < 3; $i++) {
            if ($binary_images[$i] !== null) {
                $stmt->bindValue($i + 5, $binary_images[$i], PDO::PARAM_LOB);
            } else {
                $stmt->bindValue($i + 5, null, PDO::PARAM_NULL);
            }
        }

        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

    // --- DELETE PRODUCT API ---
    elseif ($api === 'delete_product') {
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$data['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- EDIT PRODUCT API ---
    elseif ($api === 'edit_product') {
        try {
            $params = [$data['name'], $data['price'], $data['category'], $data['description']];
            if (isset($data['images']) && !empty($data['images'])) {
                $images = $data['images'];
                $decodeImg = function($img_string) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $img_string)) {
                        $img_string = substr($img_string, strpos($img_string, ',') + 1);
                    }
                    return base64_decode($img_string);
                };
                $img1 = isset($images[0]) ? $decodeImg($images[0]) : null;
                $img2 = isset($images[1]) ? $decodeImg($images[1]) : null;
                $img3 = isset($images[2]) ? $decodeImg($images[2]) : null;
                $sql = "UPDATE products SET name = ?, price = ?, category = ?, description = ?, image = IFNULL(?, image), image2 = IFNULL(?, image2), image3 = IFNULL(?, image3) WHERE id = ?";
                array_push($params, $img1, $img2, $img3, $data['id']);
            } else {
                $sql = "UPDATE products SET name = ?, price = ?, category = ?, description = ? WHERE id = ?";
                $params[] = $data['id'];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // [1] ISARA ANG DATABASE PARA SA API CALLS
    $pdo = null; 
    exit;
}

// ==========================================
// 3. PAGE LOGIC (HTML Rendering)
// ==========================================
$page = $_GET['page'] ?? 'home';
$category_filter = $_GET['category'] ?? '';
$search_query = $_GET['search'] ?? '';

function getBase64Image($data) {
    if (!$data) return 'placeholder.jpg';
    return 'data:image/jpeg;base64,' . base64_encode($data);
}

// Fetch categories and locations for UI
$categories = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
$locations = $pdo->query("SELECT * FROM locations")->fetchAll();
$featured = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4")->fetchAll();

// Main Collection Query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
if($category_filter) { $sql .= " AND category = ?"; $params[] = $category_filter; }
if($search_query) { $sql .= " AND (name LIKE ? OR description LIKE ?)"; $params[] = "%$search_query%"; $params[] = "%$search_query%"; }
$sql .= " ORDER BY id DESC";
$coll_stmt = $pdo->prepare($sql);
$coll_stmt->execute($params);
$collection = $coll_stmt->fetchAll();

// [2] ISARA ANG DATABASE DITO DAHIL TAPOS NA LAHAT NG QUERY
$pdo = null; 

// Ang sumunod na nito ay ang iyong HTML...
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>R and GHIE Furniture</title>
    <meta property="og:title" content="R and GHIE Furniture" />
    <meta name="description" content="Shop quality furniture at R and GHIE Furniture. Explore our latest collection, showrooms, and online catalog with fast local service.">
    <meta name="google-site-verification" content="Z4UyNf0cnZVCkyRFwQFLFy9PGbkHe-lx3N70VzoXj0o" />
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <link rel="manifest" href="/manifest.json">
	<meta name="theme-color" content="#A67C52">
   
  </head>
  <body>
    <div id="app" class="grid-container">
      <header>
  <a href="index.php?page=home" class='logo' align="center" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:#1A1A1A;">
    <img src="logo.png" loading="lazy" alt="logo" style="width:45px; border-radius:50%;">
    <span class="logo-text">R and GHIE Furnitures</span>
  </a>

  <div class="header-search-container">
    <form action="index.php" method="GET">
        <input type="hidden" name="page" value="collection">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">Search</button>
    </form>
</div>

  <nav class="nav-links">
    <a href="index.php?page=home" class="<?= ($page == 'home') ? 'active' : '' ?>">Home</a>
    <a href="index.php?page=collection" class="<?= ($page == 'collection') ? 'active' : '' ?>">Collection</a>
    <a href="index.php?page=showrooms" class="<?= ($page == 'showrooms') ? 'active' : '' ?>">About Us</a>
    <a href="index.php?page=tutorial" class="<?= ($page == 'tutorial') ? 'active' : '' ?>">Tutorial</a>
  </nav>

  <?php if ($page != 'admin'): ?>
    <div class='ai-pill' onclick="document.getElementById('ai-input').focus()">
      <span>Recommendation</span>
    </div>
  <?php endif; ?>
</header>

      <div class="sidebar-left">
        <h3 style="font-size:12px; text-transform:uppercase; color:#999; letter-spacing:1px; margin-bottom:12px;">Categories</h3>
        <div class="category-list">
          <a href="index.php?page=collection" class="category-item <?= !$category_filter ? 'active' : '' ?>">
            <span>All Selection</span>
            <span class="category-icon">→</span>
          </a>
          <?php foreach ($categories as $cat): ?>
          <a href="index.php?page=collection&category=<?= urlencode($cat) ?>" class="category-item <?= ($category_filter == $cat) ? 'active' : '' ?>">
            <span><?= htmlspecialchars($cat) ?></span>
            <span class="category-icon">→</span>
          </a>
          <?php endforeach; ?>
        </div>
        
        <div style="margin-top:auto; padding-top:20px; border-top:1px solid #EEE;">
          <h4 style="font-size:10px; text-transform:uppercase; color:#999; letter-spacing:1px; margin-bottom:12px;">Visit Us</h4>
          <?php foreach ($locations as $loc): ?>
            <div class="location-card" style="margin-bottom:12px;">
              <strong><?= htmlspecialchars($loc['region']) ?></strong>
              <span><?= htmlspecialchars($loc['address']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <main class='main-content'>
        <?php if ($page == 'home'): ?>
          <div id="view-home">
            <div class="hero-display">
                <div class="hero-img">
                    <?php if (!empty($featured)): ?>
                        <img src="<?= getBase64Image($featured[0]['image']) ?>" alt="<?= htmlspecialchars($featured[0]['name']) ?>" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="hero-details">
                    <h1>R &amp; Ghie Design<br></h1>
                    <p class="price-tag">Bespoke Furniture Craftsmanship</p>
                    <a href="index.php?page=collection" class="order-btn">Explore Full Collection</a>
                </div>
            </div>
            
            <div class='featured-section'>
              <h2 class='section-title' style="font-family:Georgia,serif; font-size:24px; margin-bottom:24px;">Featured Pieces</h2>
              <div class='product-grid'>
               <?php foreach ($featured as $p): ?>
  <div class="product-card" 
     div class="product-card" 
     onclick="window.location.href='index.php?page=collection#prod-<?= $p['id'] ?>'" 
     style="cursor: pointer;">
       
    <img src="<?= getBase64Image($p['image']) ?>" class="product-card-img" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
    
    <div class="product-card-info">
      <div style="align-items:start;">
        <div>
          <h3 class="product-card-title"><?= htmlspecialchars($p['name']) ?></h3>
          <p class="product-card-price">₱<?= number_format($p['price'], 2) ?></p>
        </div>
   
      </div>
    </div>
</div>
<?php endforeach; ?>
              </div>
            </div>
          </div>

        <?php elseif ($page == 'collection'): ?>
          <div class="product-grid">
  <?php foreach ($collection as $p): ?>

    <div class="product-card" id="prod-<?= $p['id'] ?>" style="scroll-margin-top: 100px;">
      
      <div class="product-image-container">
        <img src="<?= getBase64Image($p['image']) ?>" 
             id="main-img-<?= $p['id'] ?>" 
             class="product-card-img" 
             style="width: 100%; height: 250px; object-fit: cover; cursor: pointer;"
             onclick="openViewer(this.src)" 
             loading="lazy">
        
        <?php if ($p['image2'] || $p['image3']): ?>
          <div class="thumb-bar" style="display: flex; gap: 5px; padding: 10px;">
            <img src="<?= getBase64Image($p['image']) ?>" 
                 style="width: 40px; height: 40px; object-fit: cover; cursor: pointer; border: 1px solid #A67C52;" 
                 onclick="document.getElementById('main-img-<?= $p['id'] ?>').src=this.src"
                 loading="lazy">
            
            <?php if ($p['image2']): ?>
              <img src="<?= getBase64Image($p['image2']) ?>" 
                   style="width: 40px; height: 40px; object-fit: cover; cursor: pointer; border: 1px solid #ddd;" 
                   onclick="document.getElementById('main-img-<?= $p['id'] ?>').src=this.src"
                   loading="lazy">
            <?php endif; ?>
            
            <?php if ($p['image3']): ?>
              <img src="<?= getBase64Image($p['image3']) ?>" 
                   style="width: 40px; height: 40px; object-fit: cover; cursor: pointer; border: 1px solid #ddd;" 
                   onclick="document.getElementById('main-img-<?= $p['id'] ?>').src=this.src"
                   loading="lazy">
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="product-card-info" style="padding: 15px;">
          <h3 class="product-card-title"><?= htmlspecialchars($p['name']) ?></h3>
          <p class="product-card-price">₱<?= number_format($p['price'], 2) ?></p>
          <p style="font-size:12px; color:#666;"><?= htmlspecialchars($p['description']) ?></p>
<a href="https://m.me/JerGO01?text=<?= urlencode("Inquiry: " . $p['name'] . "\nItem Link: " . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?page=collection#prod-' . $p['id']) ?>" 
   class="buy-btn" 
   target="_blank"
   style="display:block; text-align:center; margin-top:10px;">
   Message Us To Buy
</a>
      </div>
    </div>

  <?php endforeach; ?>
</div>



        <?php elseif ($page == 'showrooms'): ?>
          <div id="view-showrooms">
            
            <!-- ========================================== -->
            <!-- ABOUT US SECTION (INCORPORATED STYLING)   -->
            <!-- ========================================== -->
            <div class="about-us-container" style="background:#FFF; padding:32px; border:1px solid #E5E1DA; margin-bottom:32px;">
              
              <!-- About Us Header -->
              <h2 style="font-family:Georgia,serif; font-size:32px; margin-bottom:8px; color:#1A1A1A;">About Us</h2>
              <p style="color:#666; font-size:15px; line-height:1.6; margin-bottom:28px;">
                Welcome to <strong>R and Ghie Furniture</strong>. We are committed to designing and crafting exceptional bespoke furniture pieces tailored to transform your spaces into elegant and comfortable homes.
              </p>

              <!-- Who We Are Section -->
              <div style="margin-bottom:28px; padding-bottom:20px; border-bottom:1px solid #F0EFEA;">
                <h3 style="font-family:Georgia,serif; font-size:22px; color:#A67C52; margin-bottom:10px;">Who We Are</h3>
                <p style="color:#1A1A1A; line-height:1.6; font-size:14px; margin:0;">
                  R and Ghie Furniture is a home-grown furniture design studio and workshop based in Odiongan, Romblon. Built on passion and expert craftsmanship, we specialize in high-quality, custom-made furniture built to last for generations.
                </p>
              </div>

              <!-- Mission & Vision (2-Column Grid) -->
              <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:24px; margin-bottom:28px;">
                
                <!-- Our Mission -->
                <div style="background:#F9F9F7; padding:20px; border:1px solid #EEE; border-left:4px solid #A67C52;">
                  <h3 style="font-family:Georgia,serif; font-size:20px; margin-top:0; margin-bottom:10px; color:#1A1A1A;">Our Mission</h3>
                  <p style="font-size:13px; color:#666; line-height:1.6; margin:0;">
                    To deliver premium, durable, and stylish furniture that fulfills our clients' unique needs while promoting sustainable local craftsmanship and providing reliable service.
                  </p>
                </div>

                <!-- Our Vision -->
                <div style="background:#F9F9F7; padding:20px; border:1px solid #EEE; border-left:4px solid #1A1A1A;">
                  <h3 style="font-family:Georgia,serif; font-size:20px; margin-top:0; margin-bottom:10px; color:#1A1A1A;">Our Vision</h3>
                  <p style="font-size:13px; color:#666; line-height:1.6; margin:0;">
                    To be the leading and most trusted furniture brand in the region, recognized for timeless designs, exceptional quality, and customer-first service.
                  </p>
                </div>

              </div>

              <!-- What We Offer Section -->
              <div style="margin-bottom:28px;">
                <h3 style="font-family:Georgia,serif; font-size:22px; color:#1A1A1A; margin-bottom:16px;">What We Offer</h3>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                  <div style="padding:16px; border:1px solid #F0EFEA; border-radius:4px;">
                    <h4 style="margin:0 0 8px 0; font-size:14px; color:#A67C52;">🛋️ Custom Furniture Design</h4>
                    <p style="font-size:12px; color:#666; margin:0; line-height:1.5;">Tailor-made tables, beds, cabinets, and seating options according to your dimensions and wood preference.</p>
                  </div>
                  <div style="padding:16px; border:1px solid #F0EFEA; border-radius:4px;">
                    <h4 style="margin:0 0 8px 0; font-size:14px; color:#A67C52;">🪵 Quality Woodworking</h4>
                    <p style="font-size:12px; color:#666; margin:0; line-height:1.5;">Handcrafted by skilled artisans using premium materials to ensure durability and beauty.</p>
                  </div>
                  <div style="padding:16px; border:1px solid #F0EFEA; border-radius:4px;">
                    <h4 style="margin:0 0 8px 0; font-size:14px; color:#A67C52;">💬 Consultations and Inquiries</h4>
                    <p style="font-size:12px; color:#666; margin:0; line-height:1.5;">Direct assistance via Messenger to help you choose the best pieces for your living space.</p>
                  </div>
                </div>
              </div>

              <!-- Why Choose R and Ghie Furniture -->
              <div style="background:#FDFDFD; padding:20px; border:1px dashed #A67C52; border-radius:4px;">
                <h3 style="font-family:Georgia,serif; font-size:20px; color:#1A1A1A; margin-top:0; margin-bottom:12px;">Why Choose R and Ghie Furniture?</h3>
                <ul style="padding-left:20px; margin:0; color:#666; font-size:13px; line-height:1.8;">
                  <li><strong>Handcrafted Excellence:</strong> Every piece is built with attention to detail and long-lasting quality.</li>
                  <li><strong>Affordable Bespoke Options:</strong> Get custom-designed furniture that fits your budget.</li>
                  <li><strong>Local and Accessible:</strong> Visit our local showroom or contact us directly for easy ordering and direct delivery.</li>
                </ul>
              </div>

            </div>
            <!-- ========================================== -->
            <!-- END OF ABOUT US SECTION                    -->
            <!-- ========================================== -->

            <h2 style="font-family:Georgia,serif; font-size:32px; margin-bottom:8px;">Visit Our Showroom</h2>
            <p style="color:#666; margin-bottom:32px;">Experience the quality of G.U. Furnitures pieces in person.</p>
            <div class="showrooms-view">
              <?php foreach ($locations as $loc): ?>
              <!-- Showroom Items loop continues below... -->
                <div class="showroom-item" style="background:#FFF; padding:32px; border:1px solid #E5E1DA; margin-bottom:24px;">
                  <h3 style="font-family:Georgia,serif; font-size:24px; margin-bottom:12px;"><?= htmlspecialchars($loc['region']) ?></h3>
                  <div style="display:flex; gap:24px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:200px;">
                      <h4 style="font-size:12px; text-transform:uppercase; color:#999; margin-bottom:8px;">Address</h4>
                      <p style="color:#1A1A1A; line-height:1.6;"><?= htmlspecialchars($loc['address']) ?></p>
                    </div>
                    <div style="flex:1; min-width:200px;">
                      <h4 style="font-size:12px; text-transform:uppercase; color:#999; margin-bottom:8px;">Contact Numbers</h4>
                      <p style="color:#A67C52; font-weight:600; font-size:18px;"><?= htmlspecialchars($loc['contact']) ?></p>
                      <p style="font-size:12px; color:#666; margin-top:8px;">Available for inquiries 8:00 AM - 6:00 PM</p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

        <?php elseif ($page == 'tutorial'): ?>
          <div id="view-tutorial">
            <h2 style="font-family:Georgia,serif; font-size:32px; margin-bottom:8px;">How to Order</h2>
            <p style="color:#666; margin-bottom:40px;">Follow these simple steps to bring G.U. design into your home.</p>
            
            <div class="tutorial-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:32px;">
              <div class="tutorial-card" style="padding:24px; background:#F9F9F7; border:1px solid #EEE;">
                <div style="font-size:32px; margin-bottom:16px;">🔍</div>
                <h3 style="font-family:Georgia,serif; margin-bottom:12px;">1. Browse Catalog</h3>
                <p style="font-size:14px; color:#666; line-height:1.6;">Explore our online collection or visit our showroom at 5505 Dapawan, Odiongan to see our latest handcrafted pieces.</p>
              </div>
              
              <div class="tutorial-card" style="padding:24px; background:#F9F9F7; border:1px solid #EEE;">
                <div style="font-size:32px; margin-bottom:16px;">💬</div>
                <h3 style="font-family:Georgia,serif; margin-bottom:12px;">2. Direct Inquiry</h3>
                <p style="font-size:14px; color:#666; line-height:1.6;">Click the "Buy Now" button on any item. It will automatically open Facebook Messenger with the item details for quick processing.</p>
              </div>
              
              <div class="tutorial-card" style="padding:24px; background:#F9F9F7; border:1px solid #EEE;">
                <div style="font-size:32px; margin-bottom:16px;">🛋️</div>
                <h3 style="font-family:Georgia,serif; margin-bottom:12px;">3. Customization</h3>
                <p style="font-size:14px; color:#666; line-height:1.6;">Discuss specific wood types or dimensions with our craftsmen via Messenger to tailor the piece to your specific space.</p>
              </div>
              
              <div class="tutorial-card" style="padding:24px; background:#F9F9F7; border:1px solid #EEE;">
                <div style="font-size:32px; margin-bottom:16px;">🚛</div>
                <h3 style="font-family:Georgia,serif; margin-bottom:12px;">4. Delivery</h3>
                <p style="font-size:14px; color:#666; line-height:1.6;">Once confirmed, we handle the logistics. You can also pick up directly from our Tablas Island studio.</p>
              </div>
            </div>
            
            <div style="margin-top:48px; text-align:center; padding:40px; border:2px dashed #EEE;">
              <p style="font-family:Georgia,serif; font-size:20px; color:#1A1A1A; margin-bottom:16px;">Need immediate help?</p>
              <p style="font-size:16px; color:#A67C52; font-weight:600;">📱 Call: 0956 027 3149 or 0912 773 4045</p>
            </div>
              <!-- Custom Install Button -->
<button id="pwa-install-btn" style="display:none; position:fixed; bottom:20px; right:20px; z-index:9999; padding:12px 20px; background:#A67C52; color:white; border:none; border-radius:25px; font-weight:bold; box-shadow:0 4px 8px rgba(0,0,0,0.3); cursor:pointer;">
  📲 Install App
</button>

<script>
let deferredPrompt;
const installBtn = document.getElementById('pwa-install-btn');

window.addEventListener('beforeinstallprompt', (e) => {
  // Pigilan ang default browser banner at i-save ang event
  e.preventDefault();
  deferredPrompt = e;
  
  // Ipakita ang ating custom button
  if (installBtn) {
    installBtn.style.display = 'block';
  }
});

if (installBtn) {
  installBtn.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    
    // Ipakita ang prompt ng installation
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
      console.log('User accepted the install prompt');
    }
    deferredPrompt = null;
    installBtn.style.display = 'none';
  });
}

// Itago ang button kapag na-install na
window.addEventListener('appinstalled', () => {
  if (installBtn) installBtn.style.display = 'none';
  console.log('PWA installed successfully');
});
</script>
          </div>

     <?php elseif ($page == 'admin'): ?>
    <div id="view-admin" style="padding: 20px;">
        
        <!-- LOGIN SECTION: Ito ang unang makikita -->
        <div id="admin-login-section" style="display: block; max-width: 400px; margin: 50px auto; padding: 30px; background: #fff; border: 1px solid #ddd; border-radius: 8px;">
            <h2 style="text-align: center; margin-bottom: 20px;">Admin Login</h2>
            <p id="login-error" style="color: #ff4757; display: none; text-align: center; margin-bottom: 15px;"></p>
            
            <div style="margin-bottom: 15px;">
                <label>Username</label>
                <input type="text" id="admin-user" style="width: 100%; padding: 10px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label>Password</label>
                <input type="password" id="admin-pass" style="width: 100%; padding: 10px;">
            </div>
            
            <button onclick="handleLogin()" style="width: 100%; background: #A67C52; color: white; padding: 12px; border: none; cursor: pointer;">Login</button>
        </div>

        <!-- DASHBOARD SECTION: Dito ang upload items, naka-hide muna (display: none) -->
     
<div id="admin-dashboard-section" style="display: none;">
    <h3 id="form-title">Add New Product</h3>
    
    <!-- Siguraduhin na 'edit-id' ang ID nito -->
    <input type="hidden" id="edit-id"> 
    
    <!-- Ang button ay dapat may submitProduct as default -->
    <button id="submit-btn" onclick="submitProduct()" style="background:#A67C52; color:white; padding:10px 20px;">Publish Product</button>
</div>

    <!-- Upload Form -->
    <div id="admin-container" style="background: white; padding: 20px; border: 1px solid #ddd; margin-bottom: 30px;">
        <h3 id="form-title">Add New Product</h3>
        
        <!-- DITO MO ISALPAK ITONG LINE NA ITO -->
        <input type="hidden" id="edit-id"> 
        
        <input type="text" id="p-name" placeholder="Furniture Name" style="width:100%; margin-bottom:10px;">
        <input type="number" id="p-price" placeholder="Price" style="width:100%; margin-bottom:10px;">
        <input type="text" id="p-category" placeholder="Category" style="width:100%; margin-bottom:10px;">
        <textarea id="p-desc" placeholder="Description" style="width:100%; margin-bottom:10px;"></textarea>
        
     <!-- Hanapin ang linyang ito sa iyong index.php (bandang dulo ng Admin section) -->
<div class="admin-form">
    <!-- Main Form Fields -->
    <input type="text" id="p-name" placeholder="Product Name" style="width:100%; padding:10px; margin-bottom:10px;">
    <!-- ... (ibang inputs gaya ng price/cat) ... -->

    <div style="background:#F9F9F9; padding:20px; border:2px dashed #A67C52; text-align:center;">
        <!-- Isang Button Lang -->
       
<input type="file" id="file-input" accept="image/*" multiple onchange="handleFileSelect(event)" style="display:none;">
<button type="button" onclick="document.getElementById('file-input').click()" style="background:#A67C52; color:white; padding:10px 20px;">
    Select Product Photos
</button>
<div id="preview-container" style="display:flex; gap:10px; margin-top:15px; justify-content:center;"></div>
<button id="submit-btn" onclick="submitProduct()">ADD ITEM</button>
</div>
        <!-- Siguraduhin na may ID itong button para mabago natin ang text sa JS -->
        <button id="submit-btn" onclick="submitProduct()" style="background:#A67C52; color:white; padding:10px 20px;">Publish Product</button>
    </div>

    <!-- Dito naman ang lalagyan ng inventory list table -->
    <div id="admin-inventory-list"></div>
</div>
    </div>
<?php endif; ?>
      </main>

      <div class='sidebar-right'>
        <div class='search-bar'>
          <form action="index.php" method="GET">
            <input type="hidden" name="page" value="collection">
            <input type="text" name="search" id="product-search" placeholder="Search catalog..." class="ai-input" value="<?= htmlspecialchars($search_query) ?>">
          </form>
        </div>
        <div class='ai-chat-box'>
          <h3 style="font-family:Georgia,serif; margin:0 0 10px 0; font-size:16px;">AI Assistant</h3>
          <div id="ai-messages" class="ai-message">
            <div class="ai-msg">Hello! Describe the room you're designing and I'll help you find matching furniture.</div>
          </div>
          <textarea id="ai-input" placeholder="e.g. Minimalist living room..." class="ai-input" rows="3"></textarea>
          <button class='ai-send' id="ai-btn" onclick="askAi()">Get Suggestion</button>
        </div>
      </div>

      <footer class='footer-strip'>
        <div id="step-1" class="step <?= ($page != 'tutorial') ? 'active-step' : '' ?>"><div class='step-num'>1</div>Explore</div>
        <div id="step-2" class="step"><div class='step-num'>2</div>Select</div>
        <div id="step-3" class="step <?= ($page == 'tutorial') ? 'active-step' : '' ?>"><div class='step-num'>3</div>Order via Messenger</div>
        <div style='margin-left:auto;font-size:12px;color:#999;display:flex;align-items:center;gap:12px;'>
          <span>© 2024 JerGo Furniture Design Studio</span>
            <a href="index.php?page=admin" style="text-decoration:none; opacity:0.3;" title="Upload Item">🔒</a>
          </div>
      </footer>
    </div>

      
    <!-- Overlay Viewer Structure -->
<div id="productOverlay" class="image-viewer-modal" style="display:none;">
    <div class="modal-card" style="background:white; padding:20px; border-radius:10px; max-width:600px; width:90%; position:relative;">
        <span class="close-viewer" onclick="closeProductOverlay()" style="position:absolute; right:15px; top:10px; cursor:pointer; font-size:24px;">&times;</span>
        <div class="modal-body" style="display: flex; gap: 20px; flex-wrap: wrap;">
            <img id="overlayImg" src="" style="max-width: 250px; width:100%; border-radius: 10px; object-fit:cover;" >
            <div class="details" style="flex:1; min-width:250px;">
                <h2 id="overlayName" style="margin-top:0;"></h2>
                <p id="overlayPrice" style="color: #27ae60; font-weight: bold; font-size: 1.5rem; margin:10px 0;"></p>
                <hr>
                <p id="overlayDesc" style="color:#666; line-height:1.4;"></p>
                <br>
                <a id="overlayBuyBtn" href="#" target="_blank" class="buy-btn" style="display:inline-block; text-decoration:none;">Inquire on Messenger</a>
            </div>
        </div>
    </div>
</div>

<div id="imageViewer" class="image-viewer-modal" style="display:none;" onclick="closeViewer()">
    <span class="close-viewer" style="position:absolute; right:20px; top:20px; color:white; font-size:40px;">&times;</span>
    <img class="modal-content" id="fullImage" style="max-width:90%; max-height:90%; margin:auto;" loading="lazy">
</div>
   <script>
    // Ito ang "Brain" ng AI. Kinukuha nito lahat ng products sa database mo.
    const websiteProducts = <?php echo json_encode($collection); ?>;
    console.log("AI Database Loaded:", websiteProducts); // Para ma-check mo sa F12 kung may laman
</script>
    <script type="module" src="main.js"></script>
    <script src="disck_data.js"></script>
      <script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('Service Worker Registered!', reg))
      .catch(err => console.error('Service Worker Error:', err));
  });
}
</script>
  </body>
</html>
</html>

