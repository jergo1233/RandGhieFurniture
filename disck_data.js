// disck_data.js - Ang Mas Pinatalinong "Brain" ng G.U. Design AI Assistant
window.disckContent = {
    studioInfo: {
        name: "G.U. Furniture Design Studio",
        businessName: "R and Ghie Furniture / G.U. Furnitures",
        owner: "Ginalyn Urbano",
        description: "Ang G.U. Furniture Design Studio ay eksperto sa paggawa ng bespoke at high-quality furniture gamit ang hardwood tulad ng Mahogany, Narra, Teak, at Gmelina. Pinagsasama namin ang modernong minimalist na disenyo at matibay na tradisyunal na craftsmanship ng Romblon.",
        location: "5505 Dapawan, Odiongan, Romblon (Malapit sa Pamilihang Bayan ng Odiongan).",
        hours: "Lunes hanggang Sabado, 8:00 AM – 6:00 PM. Open din sa mga scheduled chat inquiries tuwing Linggo.",
        contact: "Phone: 0956 027 3149 / 0912 773 4045 | Facebook/Messenger: Ginalyn Galangera Urbano (JerGO PH)",
        history: "Nagsimula ang studio sa pangarap na maghatid ng de-kalidad, matibay, at affordable na sining ng Romblon woodworking sa mga tahanan at establisimyento."
    },

    // 10 ACTUAL DATABASE PRODUCTS
    inventory: [
        {
            id: 1,
            name: "Handcrafted Wooden Baby Crib",
            cat: "Bedroom",
            price: 7000,
            status: "Available",
            desc: "Siguraduhin ang ligtas at komportableng tulog ng baby gamit ang aming heavy-duty wooden crib. Matibay, malinis, at safe para sa baby."
        },
        {
            id: 2,
            name: "Classic Wooden Sala Set",
            cat: "Living Room",
            price: 15000,
            status: "Available",
            desc: "Inclusions: 1 Long Sofa (3-Seater), 2 Single Armchairs, at 1 Center Table (Box-type base). Matibay at gawa sa de-kalidad na kahoy."
        },
        {
            id: 3,
            name: "Wooden Organizer & Display Set",
            cat: "Living Room",
            price: 3000,
            status: "Available",
            desc: "Base price: ₱3,000-₱4,000. Ayusin ang inyong mga gamit sa bahay gamit ang aming mga de-kalidad na wooden racks at stands. Maayos at matibay na lalagyan."
        },
        {
            id: 4,
            name: "Modern Inlay Dining Set",
            cat: "Dining",
            price: 13000,
            status: "Available",
            desc: "Upgrade your dining area with this elegant and sturdy 6-seater wooden dining set. Featuring a unique tile/glass inlay design on the tabletop."
        },
        {
            id: 5,
            name: "Elegant 6-Seater Solid Wood Dining Table Set #2",
            cat: "Dining",
            price: 13000,
            status: "Available",
            desc: "Customizable style, size, color, or design suggestions. Solid wood dining table set built for family meals and gatherings."
        },
        {
            id: 6,
            name: "Wooden Double Deck Bed",
            cat: "Bedroom",
            price: 12000,
            status: "Available",
            desc: "Space-saving and durable wooden double deck bed, perfect for kids, siblings, dorms, apartments, or family rooms. Made with quality wood."
        },
        {
            id: 7,
            name: "Elegant Queen Size Wooden Bed",
            cat: "Bedroom",
            price: 13000,
            status: "Available",
            desc: "Upgrade your bedroom with this beautifully handcrafted Queen Size bed made from quality solid wood with a smooth polished finish."
        },
        {
            id: 8,
            name: "Elegant 6-Seater Solid Wood Dining Table Set",
            cat: "Dining",
            price: 15000,
            status: "Available",
            desc: "Upgrade your dining area with this elegant 6-seater solid wood dining table set, designed with a smooth finish and modern classic style."
        },
        {
            id: 9,
            name: "Elegant Wooden Sala Set with Center Table – Modern Black & Natural Finish",
            cat: "Living Room",
            price: 15000,
            status: "Available",
            desc: "Upgrade your living room with this elegant sala set featuring a stylish combination of black accents and polished natural wood finish."
        },
        {
            id: 10,
            name: "Door With Frame (Standard Size 90 x 210 cm)",
            cat: "Fixtures",
            price: 8000,
            status: "Available",
            desc: "Solid wooden door complete with heavy-duty frame. Standard dimensions: 90 cm x 210 cm. Perfect for main doors or interior rooms."
        },
        {
            id: 11,
            name: "Wooden Bookshelf / Display Cabinet",
            cat: "Living Room",
            price: 5000,
            status: "Available",
            desc: "Sturdy wooden bookshelf with multiple shelves for storing books and displaying decorative items. Made with quality solid wood."
        },
        {
            id: 12,
            name: "Wooden Coffee Table",
            cat: "Living Room",
            price: 4500,
            status: "Available",
            desc: "Elegant and compact wooden coffee table perfect for any living room. Smooth polished finish with sturdy construction."
        },
        {
            id: 13,
            name: "Wooden Bedside Table / Nightstand",
            cat: "Bedroom",
            price: 3500,
            status: "Available",
            desc: "Compact wooden nightstand with drawer for bedroom storage. Available in various wood finishes and customizable designs."
        },
        {
            id: 14,
            name: "Wooden Desk / Study Table",
            cat: "Office",
            price: 6000,
            status: "Available",
            desc: "Spacious wooden desk perfect for home office or study area. Features sturdy legs and smooth working surface with natural wood grain."
        },
        {
            id: 15,
            name: "Wooden TV Stand / Entertainment Console",
            cat: "Living Room",
            price: 7500,
            status: "Available",
            desc: "Modern wooden TV stand with storage compartments. Designed to accommodate various TV sizes and home entertainment systems."
        }
    ],

    buyingProcess: {
        step1: "Pumili ng item sa aming online Collection o mag-send ng sarili ninyong design o sukat (custom dimensions).",
        step2: "I-click ang 'Buy Now' o 'Message Us To Buy' button para makipag-chat agad sa aming Facebook Messenger.",
        step3: "Kumpirmahin ang availability, final pricing, at shipping details kasama ang seller.",
        step4: "Magbayad ng 50% Downpayment para maikasa ang gawa/order sa aming workshop.",
        step5: "Maghintay ng 14–21 araw para sa production (kung pre-order/custom build) o 1-3 araw (kung ready-stock).",
        step6: "Iproseso ang delivery o door-to-door pickup at bayaran ang remaining 50% balance.",
        paymentMethods: ["GCash", "Bank Transfer (BDO / BPI)", "Cash on Delivery / Pickup (Odiongan Area)"],
        shippingInfo: "Nag-o-offer kami ng local delivery sa Odiongan, pati na rin sa buong Tablas Island / Romblon. Para sa labas ng lalawigan, ginagamitan ito ng wooden crate packaging via sea freight."
    },

    // COMPREHENSIVE FAQs FOR ALL BUYER QUESTIONS
    faqs: [
        // 1. PAYMENT, DISCOUNTS & INSTALLMENT
        {
            keywords: ["payment", "bayad", "mode of payment", "gcash", "cod", "downpayment"],
            category: "Payment",
            q: "Ano-ano ang mga mode of payment at proseso ng bayaran?",
            a: "Tumatanggap kami ng GCash at Cash/Cash upon Pickup/Delivery sa Odiongan. Ang standard policy ay 50% Downpayment bago simulan ang gawa, at ang nahuling 50% balance ay babayaran kapag ready na para sa delivery."
        },
        {
            keywords: ["installment", "hulugan", "layaway", "monthly", "partial"],
            category: "Payment",
            q: "Pwede po ba ang hulugan o installment?",
            a: "Ang standard policy natin ay 50% downpayment at 50% balance upon delivery. Subalit, para sa mga malalaking orders, pwede ninyong kausapin si Ma'am Ginalyn Urbano sa Messenger para sa flexible o customized payment terms."
        },
        {
            keywords: ["discount", "tawad", "mura", "promo", "less", "wholesale", "bulk"],
            category: "Payment",
            q: "Pwede po bang tumawad o makahingi ng discount?",
            a: "Opo! Pwedeng-pwede pong makipag-negotiate ng discount lalo na kung maramihang items (bulk order) o package ang bibilhin. Makipag-chat lang po kay Seller sa Messenger para sa best price offer."
        },

        // 2. CUSTOMIZATION & MATERIALS
        {
            keywords: ["custom", "sariling design", "ipagagawa", "sukat", "dimension", "customize"],
            category: "Customization",
            q: "Tumatanggap po ba kayo ng custom design o sariling sukat?",
            a: "Opo, 100% Bespoke at Customized kami! Pwede ninyong i-send ang picture, drawing, o eksaktong sukat (in inches/feet) ng gusto ninyong ipagawa sa aming Messenger para mabigyan kayo ng libreng quotation."
        },
        {
            keywords: ["kahoy", "wood", "material", "mahogany", "narra", "teak", "gmelina", "plywood"],
            category: "Materials",
            q: "Anong mga uri ng kahoy ang ginagamit ninyo?",
            a: "Ginagamit namin ang mga premium hardwoods tulad ng Mahogany, Narra, Teak, at Gmelina. Lahat ng kahoy ay dumaan sa tamang pagpapatuyo (kiln/air dried) upang makaiwas sa pag-bukbok o pagbaluktot."
        },
        {
            keywords: ["kulay", "paint", "finish", "varnish", "stain", "color", "black", "duco"],
            category: "Customization",
            q: "Pwede po bang pumili ng kulay o wood finish?",
            a: "Opo! Mayroon kaming Natural Wood Finish, Dark Walnut, Mahogany Red Stain, Matte Black, White Duco Finish, at Clear Gloss Coating. Sabihin lang ang inyong preference bago simulan ang produksyon."
        },

        // 3. SHIPPING, DELIVERY & TIMELINE
        {
            keywords: ["delivery", "shipping", "deliver", "hakot", "papadala", "magkano delivery"],
            category: "Logistics",
            q: "Magkano at paano ang delivery?",
            a: "Mayroon kaming direct delivery sa paligid ng Odiongan at karatig-bayan sa Tablas Island, Romblon. Ang delivery fee ay depende sa layo ng inyong lugar at laki ng item. Pwede ring irekta para sa shop pick-up."
        },
        {
            keywords: ["gaano katagal", "duration", "lead time", "kailan matatapos", "weeks", "days"],
            category: "Logistics",
            q: "Gaano katagal ang paggawa ng order?",
            a: "Para sa On-Hand / Ready Stock: 1-3 araw. Para sa Custom Orders o Pre-Order items: Karaniwang 14 hanggang 21 araw (2-3 linggo) depende sa hirap ng design at dami ng order."
        },

        // 4. WARRANTY, RETURNS & REPAIRS
        {
            keywords: ["warranty", "sira", "repair", "reklamo", "damage", "garantiya"],
            category: "Quality Assurance",
            q: "May warranty po ba ang inyong mga gawa?",
            a: "Tinitiyak namin ang tibay ng aming mga gawang furniture gamit ang matitibay na kahoy at reinforced joints. Kung sakaling magkaroon ng factory defect o damage sa delivery, kaagad makipag-ugnayan sa aming shop para sa maayos na pagpapapalit o pagpapagawa."
        },
        {
            keywords: ["return", "refund", "soli", "palit"],
            category: "Quality Assurance",
            q: "Ano ang policy para sa return o refund?",
            a: "Pinapakita at kinukumpirma muna namin sa buyer ang tapos na produkto bago ideliver. Sakaling may hindi nasunod sa agreed specifications o may pinsala sa delivery, i-check agad bago tanggapin para maayos agad ng aming team."
        },

        // 5. MAINTENANCE & CARE TIPS
        {
            keywords: ["alaga", "linis", "maintain", "bukbok", "uod", "basa"],
            category: "Maintenance",
            q: "Paano alagaan ang solid wood furniture para hindi mabukbok o masira?",
            a: "Iwasang mababad nang matagal sa direktang tubig o usok. Punasan ito ng malinis at bahagyang basang basahan. Para manatiling kumikinang ang finish, pwedeng pahiran ng wood wax o furniture polish tulad ng Pledge tuwing 3-6 na buwan."
        },

        // 6. PHYSICAL SHOWROOM & LOCATION
        {
            keywords: ["puntahan", "showroom", "shop", "address", "saan ang shop", "makita", "lokasyon", "location", "lukasyon", "lugar" ],
            category: "Store Visit",
            q: "Pwede po bang bisitahin ang inyong Showroom o Workshop?",
            a: "Opo! Bisitahin kami sa 5505 Dapawan, Odiongan, Romblon. Bukas kami mula Lunes hanggang Sabado (8:00 AM - 6:00 PM) para personal ninyong ma-inspeksyon ang kalidad ng aming mga gawang kahoy."
        },
        {
            keywords: ["bibili", "buy", "order", "purchase", "magkano", "price", "online order", "online"],
            category: "Payment",
            q: "ok ba po ang mag-order online at magbayad sa inyo?",
            a: "Opo! Bisitahin kami sa Facebook Messenger (Ginalyn Galangera Urbano) para makipag-chat at kumpirmahin ang availability, presyo, at shipping details. Maaari rin kayong magbayad ng 50% downpayment online at ang natitirang 50% ay babayaran sa delivery o pickup. pindutin lang ang message as to buy button sa bawat item para makipag-chat sa amin."
        }
        


    ]
};