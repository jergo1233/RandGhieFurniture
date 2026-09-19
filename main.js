// main.js - G.U. Furniture Design Studio Logic

// ==========================================
// GLOBAL VARIABLES & STATE

window.adminInventoryData = [];
window.adminInventoryLoading = false;

// ==========================================
// NAVIGATION & UTILS
// ==========================================
export function navigate(page) {
    window.location.href = `index.php?page=${page}`;
}// Siguraduhin na ang mga ito ay GLOBALLY ACCESSIBLE
// Dapat nasa labas ito ng anumang block para maging global
// Sample logic para sa iyong JavaScript sa main.js o sa dulo ng index.php
function openProductOverlay(id, name, price, desc, imgSrc) {
    document.getElementById('overlayName').innerText = name;
    document.getElementById('overlayPrice').innerText = "₱" + price;
    document.getElementById('overlayDesc').innerText = desc;
    document.getElementById('overlayImg').src = imgSrc;
    
    // I-update ang Messenger link na may kasamang Item Name
    const messengerBase = "https://m.me/jerome.urbano.140?text=";
    const message = encodeURIComponent("Inquiry for Item: " + name + "\nLink: " + window.location.origin + window.location.pathname + "?page=collection#prod-" + id);
    document.getElementById('overlayBuyBtn').href = messengerBase + message;
    
    document.getElementById('productOverlay').style.display = 'flex';
}

window.closeProductOverlay = function() {
    document.getElementById('productOverlay').style.display = "none";
};

window.openViewer = function(src) {
    console.log("Zooming image...");
    const modal = document.getElementById('imageViewer');
    const fullImg = document.getElementById('fullImage');
    if (modal && fullImg) {
        fullImg.src = src;
        modal.style.display = "flex";
    }
};

window.closeViewer = function() {
    document.getElementById('imageViewer').style.display = "none";
};
window.logout = function() {
    sessionStorage.removeItem('isAdmin');
    window.location.href = 'index.php?page=admin';
}

// ==========================================
// AI ASSISTANT LOGIC (G.U. DESIGN AI)
// ==========================================
window.askAi = function() {
    const inputEl = document.getElementById('ai-input');
    const btn = document.getElementById('ai-btn');
    const container = document.getElementById('ai-messages');

    // Mabilis na check kung naroroon ang mga elemento
    if (!inputEl || !container) {
        console.error("Missing required DOM elements: #ai-input or #ai-messages");
        return;
    }

    const originalText = inputEl.value.trim();
    if (!originalText) return; // Wag ituloy kung walang laman

    // 1. Idisplay ang mensahe ng user
    const userDiv = document.createElement('div');
    userDiv.style.cssText = "background:#A67C52; color:white; padding:10px; margin:5px 0; border-radius:10px; align-self:flex-end; font-size:14px; max-width:80%; margin-left:auto;";
    userDiv.innerText = originalText;
    container.appendChild(userDiv);

    inputEl.value = '';
    if (btn) btn.disabled = true;

    // 2. Iproseso ang AI Suggestion Response
    setTimeout(() => {
        const aiDiv = document.createElement('div');
        aiDiv.style.cssText = "background:#F5F5F5; color:#333; padding:10px; margin:5px 0; border-radius:10px; align-self:flex-start; font-size:14px; border:1px solid #DDD; max-width:85%;";
        
        let response = "";
        const userText = originalText.toLowerCase();

        // Ligtas na pagkuha sa disckContent
        const content = window.disckContent || {};
        const studio = content.studioInfo || {};
        const inventory = content.inventory || [];
        const faqs = content.faqs || [];

        // Matching logic
        const matchedFaq = faqs.find(faq => 
            faq.keywords && faq.keywords.some(kw => userText.includes(kw.toLowerCase()))
        );

        if (matchedFaq) {
            response = `💡 **${matchedFaq.q}**\n\n${matchedFaq.a}`;
        } else {
            const matches = inventory.filter(item => 
                userText.includes(item.name.toLowerCase()) || 
                userText.includes(item.cat.toLowerCase())
            );

            if (matches.length > 0) {
                response = "Mayroon kaming mga ganitong item sa aming collection:\n\n";
                matches.forEach(item => {
                    response += `✨ **${item.name}** - ₱${Number(item.price).toLocaleString()}\n`;
                });
            } else {
                response = "Maaari kayong magtanong tungkol sa aming **location**, **presyo ng furniture**, **custom designs**, o **delivery**.";
            }
        }

        aiDiv.innerHTML = response.replace(/\n/g, '<br>');
        container.appendChild(aiDiv);
        
        if (btn) btn.disabled = false; // Ibalik ang button responsiveness
        container.scrollTop = container.scrollHeight;
    }, 400);
};

// ==========================================
// ADMIN LOGIC (RENDER & AUTH)
// ==========================================
export async function renderAdmin() {
    const adminView = document.getElementById('view-admin');
    const isAdmin = sessionStorage.getItem('isAdmin');

    if (!adminView) return;

    if (!isAdmin) {
        adminView.innerHTML = `
            <div id="admin-login-section" style="max-width:400px; margin:60px auto; background:#FFF; padding:32px; border:1px solid #E5E1DA; border-radius:8px;">
                <h2 style="font-family:serif; text-align:center;">Seller Login</h2>
                <form id="login-form">
                    <input type="text" id="admin-user" placeholder="Username" required style="width:100%; padding:12px; margin-bottom:16px; border:1px solid #DDD;">
                    <input type="password" id="admin-pass" placeholder="Password" required style="width:100%; padding:12px; margin-bottom:24px; border:1px solid #DDD;">
                    <button type="submit" id="login-btn" style="width:100%; padding:12px; background:#A67C52; color:white; border:none; cursor:pointer; font-weight:bold;">Enter Studio</button>
                    <p id="login-error" style="color:red; font-size:12px; margin-top:12px; text-align:center; display:none;"></p>
                </form>
            </div>`;
        document.getElementById('login-form').onsubmit = handleAdminLogin;
} else {
    adminView.innerHTML = `
        <div id="admin-dashboard-section" style="max-width:800px; margin:40px auto; padding:20px;">
            <div class="admin-form" style="background:#fff; padding:32px; border-radius:10px; border:1px solid #E5E1DA; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h2 id="form-title" style="font-family:serif;">Add New Furniture</h2>
                    <a href="#" onclick="logout(); return false;" style="color:#e74c3c; font-size:14px; text-decoration:none; font-weight:bold;">Sign Out</a>
                </div>
                
                <input type="hidden" id="edit-id"> 
                <input type="text" id="p-name" placeholder="Product Name" style="width:100%; padding:12px; margin-bottom:12px; border:1px solid #ddd;">
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <input type="number" id="p-price" placeholder="Price (PHP)" style="width:100%; padding:12px; border:1px solid #ddd;">
                    <input type="text" id="p-category" placeholder="Category" style="width:100%; padding:12px; border:1px solid #ddd;">
                </div>
                
                <textarea id="p-desc" placeholder="Product Description" style="width:100%; padding:12px; margin-bottom:20px; border:1px solid #ddd; height:100px;"></textarea>
                
                <div style="background:#F9F9F9; padding:20px; border:2px dashed #A67C52; border-radius:8px; margin-bottom:20px; text-align:center;">
                    <input type="file" id="file-input" accept="image/*" multiple style="display:none;">
                    <button type="button" onclick="document.getElementById('file-input').click()" style="background:#A67C52; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
                        + Select Product Photos
                    </button>
                    <div id="preview-container" style="display:flex; gap:10px; margin-top:15px; justify-content:center;"></div>
                </div>

                <button onclick="window.submitProduct()" id="submit-btn" style="width:100%; padding:15px; background:#27ae60; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; font-size:16px;">ADD PHOTOS</button>
                <p id="admin-msg" style="text-align:center; margin-top:15px;"></p>
            </div>
            <div id="admin-inventory-list" style="margin-top:40px;"></div>
        </div>`;

    document.getElementById('file-input').addEventListener('change', handleFileSelect);
    loadInventoryTable();
}
}

async function handleAdminLogin(e) {
    e.preventDefault();
    const user = document.getElementById('admin-user').value;
    const pass = document.getElementById('admin-pass').value;
    const errorMsg = document.getElementById('login-error');
    const btn = document.getElementById('login-btn');

    btn.innerText = "Verifying...";
    btn.disabled = true;

    try {
        const response = await fetch('index.php?api=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: user, password: pass })
        });

        const result = await response.json();

        if (result.success) {
            sessionStorage.setItem('isAdmin', 'true');
            renderAdmin();
        } else {
            errorMsg.style.display = 'block';
            errorMsg.innerText = result.message || "Invalid credentials!";
            btn.innerText = "Enter Studio";
            btn.disabled = false;
        }
    } catch (err) {
        errorMsg.style.display = 'block';
        errorMsg.innerText = "Server connection failed.";
        btn.disabled = false;
        btn.innerText = "Enter Studio";
    }
}

// ==========================================
// IMAGE & PREVIEW LOGIC
// ==========================================


// Siguraduhing globally declared ito sa taas ng main_4.js[cite: 8]
let selectedFiles = []; 
// --- UPDATE: Pinahusay na File Selection para sa Multiple Images ---
function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    if (files.length === 0) return;

    // Check kung puno na (3 images limit)
    if (selectedFiles.length >= 3) {
        alert("Maximum of 3 photos only.");
        return;
    }

    // Idagdag ang bawat piniling file sa ating global array
    files.forEach(file => {
        if (selectedFiles.length < 3) {
            selectedFiles.push(file);
        }
    });

    renderPreviews(); // I-update ang display ng images
    e.target.value = ''; // I-clear ang input para mapili ulit ang parehong file kung gusto
}

// Bagong function para i-display ang mga napiling photos
function renderPreviews() {
    const container = document.getElementById('preview-container');
    if (!container) return;
    container.innerHTML = ''; 

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (event) => {
            const div = document.createElement('div');
            div.style.cssText = "position:relative; width:80px; height:80px; border:1px solid #A67C52; border-radius:4px; overflow:hidden;";
            div.innerHTML = `
                <img src="${event.target.result}" style="width:100%; height:100%; object-fit:cover;">
                <button onclick="removePhoto(${index})" style="position:absolute; top:0; right:0; background:red; color:white; border:none; cursor:pointer; font-size:10px; padding:2px 5px;">X</button>
                <p style="font-size:9px; background:rgba(166, 124, 82, 0.8); color:white; position:absolute; bottom:0; width:100%; margin:0; text-align:center;">Photo ${index + 1}</p>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// Function para makapagtanggal ng photo sa preview kung nagkamali
window.removePhoto = function(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
};
window.submitProduct = async function() {
    const name = document.getElementById('p-name').value;
    const price = document.getElementById('p-price').value;
    const cat = document.getElementById('p-category').value;
    const desc = document.getElementById('p-desc').value;
    const btn = document.getElementById('submit-btn');

    if (!name || selectedFiles.length === 0) {
        alert("Pangalan at kahit isang larawan ay kailangan.");
        return;
    }

    btn.disabled = true;
    btn.innerText = "Saving to Database...";
    
    try {
        // I-convert ang lahat ng selected files sa base64
        const base64Promises = selectedFiles.map(file => {
            return new Promise(resolve => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });
        });

        const allImages = await Promise.all(base64Promises);

        const res = await fetch('index.php?api=add_product',{
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                name, 
                price, 
                category: cat, 
                description: desc, 
                images: allImages // Ito ang papasok sa image, image2, image3 ng PHP
            })
        });

        const result = await res.json();
        if (result.success) {
            alert("Successfully added to database!");
            location.reload();
        } else {
            alert("Error: " + result.message);
        }
    } catch (err) { 
        alert("Upload failed."); 
    } finally { 
        btn.disabled = false; 
        btn.innerText = "ADD PHOTOS"; 
    }
};
// IMPORTANTE: I-bind ang listener sa pag-load[cite: 8]
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('file-input');
    if (input) {
        input.addEventListener('change', window.handleFileSelect);
    }
});

// 4. Importante: I-bind ang event listener pag-load ng page


// ==========================================
// INVENTORY CRUD
// ==========================================
async function loadInventoryTable() {
    if (window.adminInventoryLoading) {
        alert("Inventory data is still loading. Please wait a moment.");
        return;
    }

    window.adminInventoryLoading = true;

    try {
        const res = await fetch('index.php?api=products');
        const products = await res.json();
        const container = document.getElementById('admin-inventory-list');
        if (!container) return;

        window.adminInventoryData = [];
        let html = `<table style="width:100%; border-collapse: collapse; margin-top: 20px; background: white; color: #333;">
            <thead>
                <tr style="background: #f4f4f4; text-align: left;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Image</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Name</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
                </tr>
            </thead><tbody>`;

        products.forEach(p => {
            window.adminInventoryData[p.id] = p;

            // Pagsasaayos ng Image Source (File Path vs Base64 vs Placeholder)
    let imgSrc = 'placeholder.jpg';
if (p.image && p.image.trim() !== '') {
    // Kung buong URL o base64, gamitin agad
    if (p.image.startsWith('data:image') || p.image.startsWith('http')) {
        imgSrc = p.image;
    } else {
        // Siguraduhing tamang relative path ang nakukuha
        imgSrc = p.image.startsWith('/') ? p.image.substring(1) : p.image;
    }
}

            html += `<tr>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <img src="${imgSrc}" style="width:50px; height:50px; object-fit:cover; border-radius:4px;" onerror="this.src='placeholder.jpg'">
                </td>
                <td style="padding: 10px; border: 1px solid #ddd;">${p.name}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">₱${Number(p.price).toLocaleString()}</td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <button onclick="window.prepareEdit(${p.id})" style="background:#2ecc71; color:white; border:none; padding:5px 10px; cursor:pointer; margin-right:5px; border-radius:3px;">Edit</button>
                    <button onclick="window.deleteProduct(${p.id})" style="background:#e74c3c; color:white; border:none; padding:5px 10px; cursor:pointer; border-radius:3px;">Delete</button>
                </td>
            </tr>`;
        });
        container.innerHTML = html + `</tbody></table>`;
    } catch (e) {
        console.error("Table Load Error:", e);
        alert("Failed to load inventory data. Please refresh the page.");
    } finally {
        window.adminInventoryLoading = false;
    }
}
window.prepareEdit = function(id) {
    if (window.adminInventoryLoading) {
        alert("Inventory data is still loading. Please wait a moment.");
        return;
    }

    const product = window.adminInventoryData[id];
    if (!product) return;

    selectedFiles = [];
    document.getElementById('form-title').innerText = "Update Product";
    document.getElementById('edit-id').value = product.id;
    document.getElementById('p-name').value = product.name;
    document.getElementById('p-price').value = product.price;
    document.getElementById('p-category').value = product.category;
    document.getElementById('p-desc').value = product.description || "";
    
    const btn = document.getElementById('submit-btn');
    btn.innerText = "UPDATE PHOTOS"; // Palit label
    btn.style.background = "#2980b9";
    btn.onclick = window.updateProduct;
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
window.updateProduct = async function() {
    const id = document.getElementById('edit-id').value;
    const name = document.getElementById('p-name').value;
    const price = document.getElementById('p-price').value;
    const cat = document.getElementById('p-category').value;
    const desc = document.getElementById('p-desc').value;
    const btn = document.getElementById('submit-btn');

    btn.disabled = true;
    btn.innerText = "Updating...";

    try {
        let updateData = { id, name, price, category: cat, description: desc };

        // I-convert ang lahat ng nasa preview (selectedFiles)
        if (selectedFiles.length > 0) {
            const base64Promises = selectedFiles.map(file => {
                return new Promise(resolve => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(file);
                });
            });
            updateData.images = await Promise.all(base64Promises);
        }

        // DAPAT 'edit_product' ang API name dito para tumugma sa PHP
        const res = await fetch('index.php?api=edit_product', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updateData)
        });

        const result = await res.json();
        if (result.success) {
            alert("Update Success!");
            location.reload();
        } else {
            alert("Error: " + result.message);
        }
    } catch (err) {
        console.error(err);
        alert("Update Failed: Check Connection");
    } finally {
        btn.disabled = false;
        btn.innerText = "UPDATE PHOTOS";
    }
};
window.deleteProduct = async function(id) {
    if (window.adminInventoryLoading) {
        alert("Inventory data is still loading. Please wait a moment.");
        return;
    }

    if (!confirm("Sigurado ka bang gusto mong burahin ang produktong ito?")) return;

    try {
        const res = await fetch('index.php?api=delete_product', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify({ id: id })
        });

        const result = await res.json();
        if (result.success) {
            alert("Product deleted!");
            loadInventoryTable();
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) { console.error("Delete Error:", error); }
};

// INITIALIZATION
// ==========================================
window.navigate = navigate;
window.renderAdmin = renderAdmin;

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.search.includes('page=admin')) renderAdmin();
});
$(document).on('click', '.product-card', function() {
    const productId = $(this).data('id');
    showProductOverlay(productId); // Tawagin ang function para sa overlay
});
// Sample integration logic para sa main.js
const faqMatch = disckContent.faqs.find(f => userText.includes(f.q.toLowerCase()));
if (faqMatch) {
    response = faqMatch.a;
} else if (userText.includes("custom")) {
    response = disckContent.faqs.find(f => f.q === "Custom design").a;
}
