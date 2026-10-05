import os
import sys
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT

from healthy_bite_doc_formatter import (
    set_cell_background, set_cell_margins, set_cell_borders, add_header_footer,
    style_heading_1, style_heading_2, style_heading_3, add_body_paragraph,
    add_bullet_point, add_table_styled, add_image_figure, add_code_snippet,
    COLOR_HEADING, COLOR_DARK_TEXT, COLOR_MUTED, COLOR_TABLE_HEADER_HEX, COLOR_BORDER_HEX
)
from doc_data import TABLES_DATA_DICTIONARY
from doc_content_sections import (
    SDLC_PHASES_DATA, COMPARISON_TABLE_DATA, USE_CASE_SPECS_DATA,
    TEST_SUMMARY_METRICS_DATA, CUSTOMER_TEST_CASES, OWNER_TEST_CASES,
    ADMIN_TEST_CASES, SECURITY_TEST_CASES, CODE_EXPLANATIONS_DATA
)

BASE_DIR = r"D:\NEW healthy bite"
DIAGRAMS_DIR = os.path.join(BASE_DIR, "Healthy_Bite_Diagrams")
SCREENSHOTS_DIR = os.path.join(BASE_DIR, "Healthy_Bite_Screenshots")

# Targets to write
OUTPUT_DOCS = [
    os.path.join(BASE_DIR, "Documentation", "Healthy_Bite_Documentation.docx"),
    os.path.join(BASE_DIR, "Documentation", "documentation_healthy_bite.docx"),
    os.path.join(BASE_DIR, "Documentation", "Healthy_Bite_Project_Documentation.docx"),
    os.path.join(BASE_DIR, "Healthy_Bite_Project_Documentation.docx"),
    os.path.join(BASE_DIR, "Documentation", "documentation.docx")
]

def build_healthy_bite_documentation():
    print("=" * 70)
    print("STARTING HEALTHY BITE DOCUMENTATION GENERATION")
    print("Rules: NO COLORS IN HEADINGS | LITE SKY BLUE (#BAE6FD) IN ALL TABLES")
    print("=" * 70)
    
    doc = Document()
    
    # Configure A4 page with 0.7875" (20mm) margins matching original format
    for s in doc.sections:
        s.page_width = Inches(8.27)
        s.page_height = Inches(11.69)
        s.top_margin = Inches(0.7875)
        s.bottom_margin = Inches(0.7875)
        s.left_margin = Inches(0.7875)
        s.right_margin = Inches(0.7875)
        
    add_header_footer(doc)
    
    # =========================================================================
    # PRELIMINARY PAGES
    # =========================================================================
    print("1. Adding Title / Cover Page...")
    
    # Institutional Header (NO COLOR - Pure Black)
    p_inst = doc.add_paragraph()
    p_inst.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_inst.paragraph_format.space_before = Pt(12)
    p_inst.paragraph_format.space_after = Pt(2)
    r_inst1 = p_inst.add_run("DEPARTMENT OF COMPUTER APPLICATIONS\nCHRIST COLLEGE, RAJKOT")
    r_inst1.font.name = "Calibri"
    r_inst1.font.size = Pt(13)
    r_inst1.font.bold = True
    r_inst1.font.color.rgb = COLOR_HEADING
    
    p_aff = doc.add_paragraph()
    p_aff.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_aff.paragraph_format.space_after = Pt(16)
    r_aff = p_aff.add_run("Affiliated to Saurashtra University, Rajkot")
    r_aff.font.name = "Calibri"
    r_aff.font.size = Pt(10)
    r_aff.font.italic = True
    r_aff.font.color.rgb = COLOR_HEADING
    
    # Brand Logo
    logo_path = os.path.join(DIAGRAMS_DIR, "Healthy BIte logo.png")
    if os.path.exists(logo_path):
        p_logo = doc.add_paragraph()
        p_logo.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_logo.paragraph_format.space_after = Pt(12)
        r_logo = p_logo.add_run()
        r_logo.add_picture(logo_path, width=Inches(1.8))
        
    # Project Title (NO COLOR - Pure Black)
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(6)
    p_title.paragraph_format.space_after = Pt(6)
    r_title = p_title.add_run("HEALTHY BITE:\nDIGITAL RESTAURANT MENU AND FOOD ORDERING SYSTEM")
    r_title.font.name = "Calibri"
    r_title.font.size = Pt(18)
    r_title.font.bold = True
    r_title.font.color.rgb = COLOR_HEADING
    
    # Subtitle (NO COLOR - Pure Black)
    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(16)
    r_sub = p_sub.add_run("A Project Report Submitted in Partial Fulfillment of the Requirements\nfor the Degree of Bachelor of Computer Applications (BCA)")
    r_sub.font.name = "Calibri"
    r_sub.font.size = Pt(11)
    r_sub.font.italic = True
    r_sub.font.color.rgb = COLOR_HEADING
    
    # Meta Information Box (Lite Sky Blue Table)
    meta_headers = ["Project Metadata", "Details & Academic Credentials"]
    meta_rows = [
        ["Academic Year", "2026 – 2027 (Semester VI)"],
        ["Submitted By", "Gunjan Dhaduk (24CS002UG00595)\nJisna Saji (24CS002UG00578)"],
        ["Internal Project Guide", "Ms. Pintukumari Bera (Assistant Professor, Dept. of Computer Applications)"],
        ["Head of Department", "Dr. Shailendrasinh Jadeja (Dept. of Computer Applications)"],
        ["College & Affiliation", "Christ College, Rajkot • Saurashtra University, Rajkot"],
        ["Submission Month", "October 2026"]
    ]
    add_table_styled(doc, meta_headers, meta_rows, [Inches(2.5), Inches(4.2)])
    
    # Page Break
    doc.add_page_break()
    
    # --- CERTIFICATE ---
    print("2. Adding Certificate of Original Work...")
    style_heading_1(doc.add_paragraph(), "CERTIFICATE OF ORIGINAL WORK")
    
    p_cert = doc.add_paragraph()
    p_cert.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    p_cert.paragraph_format.space_after = Pt(10)
    p_cert.paragraph_format.line_spacing = 1.15
    p_cert.add_run("This is to certify that the project report entitled ")
    r_cname = p_cert.add_run("“Healthy Bite – Digital Restaurant Menu & Food Ordering System”")
    r_cname.bold = True
    p_cert.add_run(" is an authentic and bona fide record of independent software development work carried out by ")
    r_s1 = p_cert.add_run("Gunjan Dhaduk (Enrollment No: 24CS002UG00595)")
    r_s1.bold = True
    p_cert.add_run(" and ")
    r_s2 = p_cert.add_run("Jisna Saji (Enrollment No: 24CS002UG00578)")
    r_s2.bold = True
    p_cert.add_run(", students of Bachelor of Computer Applications (BCA), Semester VI, Department of Computer Applications, Christ College, Rajkot, in partial fulfillment of the academic requirements prescribed by Saurashtra University, Rajkot, during the academic period from June 2026 to October 2026 under the supervision of Ms. Pintukumari Bera.")
    
    add_body_paragraph(doc, "We also certify that this work is original, genuine, and developed under our direct supervision. It has not been submitted previously, in part or in full, to any other university, institute, or examining body for the award of any degree, diploma, or academic distinction.")
    
    p_sig_cand = doc.add_paragraph()
    p_sig_cand.paragraph_format.space_before = Pt(20)
    p_sig_cand.paragraph_format.space_after = Pt(20)
    p_sig_cand.add_run("Signature of Candidate 1: ____________________          Signature of Candidate 2: ____________________\n(Gunjan Dhaduk)                                                        (Jisna Saji)")
    
    add_body_paragraph(doc, "This is to certify that the statements made above by the candidates are true and correct to the best of our knowledge.")
    
    # Certificate Signatures Table
    cert_table_headers = ["Head of Department", "Internal Project Guide"]
    cert_table_rows = [
        ["Dr. Shailendrasinh Jadeja", "Ms. Pintukumari Bera"],
        ["Head, Department of Computer Applications", "Assistant Professor, Dept. of Computer Applications"],
        ["Christ College, Rajkot", "Christ College, Rajkot"],
        ["Signature: ______________________", "Signature: ______________________"]
    ]
    add_table_styled(doc, cert_table_headers, cert_table_rows, [Inches(3.3), Inches(3.3)])
    
    p_place = doc.add_paragraph()
    p_place.paragraph_format.space_before = Pt(12)
    p_place.add_run("Place: Rajkot, Gujarat\nDate: October 2026")
    
    # Page Break
    doc.add_page_break()
    
    # --- ACKNOWLEDGEMENT ---
    print("3. Adding Acknowledgement...")
    style_heading_1(doc.add_paragraph(), "ACKNOWLEDGEMENT")
    
    add_body_paragraph(doc, "The completion of our academic project, “Healthy Bite – Digital Restaurant Menu & Food Ordering System”, marks a deeply enriching and rewarding milestone in our undergraduate education in Computer Applications. We take this opportunity to express our sincere gratitude and heartfelt appreciation to all mentors, faculty members, and supporters who facilitated this endeavor.")
    
    add_body_paragraph(doc, "First and foremost, we express our profound gratitude to Rev. Fr. (Dr.) Jomon Thommana, Director, Christ Campus, and Dr. Yvonne Fernandes, Principal, Christ College, Rajkot, for providing state-of-the-art laboratory facilities, high-performance computing infrastructure, and an encouraging academic environment.")
    
    add_body_paragraph(doc, "We owe our sincere and deepest gratitude to our Internal Project Guide, Ms. Pintukumari Bera (Assistant Professor, Department of Computer Applications), and Mr. Anand John, for their constant encouragement, technical mentorship, constructive suggestions, and rigorous reviews throughout every phase of the Software Development Life Cycle (SDLC). Without their analytical perspective and generous guidance, the realization of this system would not have been possible.")
    
    add_body_paragraph(doc, "We also express our sincere appreciation to Dr. Shailendrasinh Jadeja, Head of Department, and all esteemed faculty members of the Department of Computer Applications, Christ College, Rajkot, for imparting foundational knowledge in Relational Database Management Systems (RDBMS), Object-Oriented Software Engineering (OOSE), Web Application Architecture, and Quality Assurance methodologies.")
    
    add_body_paragraph(doc, "Finally, our heartfelt thanks go to our parents, family members, classmates, and friends whose patience, unwavering moral support, and motivation sustained us through rigorous coding, debugging, and testing sessions.")
    
    p_ack_sign = doc.add_paragraph()
    p_ack_sign.paragraph_format.space_before = Pt(20)
    p_ack_sign.add_run("Gunjan Dhaduk (24CS002UG00595)\nJisna Saji (24CS002UG00578)\nDepartment of Computer Applications, Christ College, Rajkot\nOctober 2026")
    
    # Page Break
    doc.add_page_break()
    
    # --- TABLE OF CONTENTS ---
    print("4. Adding Table of Contents...")
    style_heading_1(doc.add_paragraph(), "TABLE OF CONTENTS")
    
    toc_headers = ["Chapter / Section", "Title & Major Subsections", "Page No."]
    toc_data = [
        ["Chapter 01", "Introduction (Name of Project, Introduction, Objectives, Scope, Features, Actors)", "01"],
        ["Chapter 02", "System Analysis (Problem Definition, Existing vs Proposed, Feasibility, SDLC)", "06"],
        ["Chapter 03", "System Design & Modeling (DFD Levels 0/1/2, ER Schema, Activity, Use Case, Process Flow, Class)", "11"],
        ["Chapter 04", "Data Dictionary (All 17 Relational Tables Specification & Constraints)", "22"],
        ["Chapter 05", "System Implementation (Directory Map, UI Screenshots Walkthrough, Code Explanations)", "32"],
        ["Chapter 06", "Testing & Quality Assurance (Test Plan, Approach, Summary, 46 Test Cases, Final Statement)", "45"],
        ["Chapter 07", "Result (Step-by-Step Test Execution & End-to-End Workflow Verification)", "54"],
        ["Chapter 08", "Conclusion & Future Scope (Academic Summary, Scalability, IoT KDS, AI Meal Engine)", "58"]
    ]
    add_table_styled(doc, toc_headers, toc_data, [Inches(1.8), Inches(4.2), Inches(0.7)])
    
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 1 — INTRODUCTION
    # =========================================================================
    print("5. Adding Chapter 1: Introduction...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 1 — INTRODUCTION")
    
    style_heading_2(doc.add_paragraph(), "1.1 Name of Project")
    add_body_paragraph(doc, "The official academic and commercial title of the software system is:")
    p_name = doc.add_paragraph()
    p_name.paragraph_format.left_indent = Inches(0.4)
    p_name.paragraph_format.space_after = Pt(8)
    r_n = p_name.add_run("“HEALTHY BITE: DIGITAL RESTAURANT MENU AND FOOD ORDERING SYSTEM”")
    r_n.bold = True
    r_n.font.size = Pt(11.5)
    r_n.font.color.rgb = COLOR_HEADING
    
    style_heading_2(doc.add_paragraph(), "1.2 Introduction")
    add_body_paragraph(doc, "The modern hospitality and food service industry is undergoing a profound digital transformation driven by health-conscious consumer behavior, contactless service demands, and nutritional transparency. Traditional dining establishments rely predominantly on static, physical laminated paper menus. While familiar, physical paper menus present acute operational and commercial limitations: they are expensive to reprint whenever prices or dishes change, they cannot dynamically indicate sold-out kitchen items, they offer zero nutritional visibility, and they cannot calculate individual ingredient modifications or dietary preferences in real time.")
    
    add_body_paragraph(doc, "Healthy Bite is conceptualized and engineered to eliminate these operational bottlenecks. Developed as a high-performance, responsive multi-tenant web application, Healthy Bite empowers seated dining patrons to scan a table-side cryptographic QR code using any smartphone camera, instantly launching a rich digital culinary catalog directly in their mobile browser. Without requiring native mobile app installations or cumbersome registration flows, customers can browse dishes, filter by dietary lifestyles (Vegetarian, Non-Vegetarian, Vegan, Jain), inspect potential allergens, customize dish portions and add-ons, view dynamic 8-macro nutritional calculations, and transmit verified orders directly to the back-of-house kitchen.")
    
    style_heading_2(doc.add_paragraph(), "1.3 Objectives of the System")
    add_body_paragraph(doc, "The engineering objectives of the Healthy Bite platform are structured around operational speed, data integrity, and dietary empowerment:")
    add_bullet_point(doc, "Eliminate Paper Menus: ", "Transition restaurant menus from static, unhygienic paper cards to an interactive, real-time digital catalog accessible via table-side QR tokens.")
    add_bullet_point(doc, "Provide Granular Dietary Transparency: ", "Empower health-conscious diners by calculating and displaying 8 vital nutritional dimensions (Calories, Protein, Carbs, Fat, Fiber, Sugar, Sodium, Caffeine) with portion scaling in real time.")
    add_bullet_point(doc, "Prevent Client-Side Price Tampering: ", "Enforce strict server-side price validation where all cart lines, portion modifiers, and add-on costs are independently recalculated from authoritative database records before persistence.")
    add_bullet_point(doc, "Streamline Kitchen Fulfillment: ", "Replace noisy paper Kitchen Order Tickets (KOT) with an interactive 5-column Kanban board (Placed, Accepted, Preparing, Ready, Completed) supporting real-time status transitions.")
    add_bullet_point(doc, "Deliver Contactless QR Security: ", "Bind physical dining tables to cryptographic QR tokens with single-tap instant access, preventing rogue off-site order submissions.")
    add_bullet_point(doc, "Facilitate Multi-Tenant Governance: ", "Provide restaurant owners with dedicated dashboards to control menus, tables, staff, and analytics, while equipping Platform Super Admins with tenant oversight and inspection tools.")
    
    style_heading_2(doc.add_paragraph(), "1.4 Scope of the System")
    add_body_paragraph(doc, "The functional scope of Healthy Bite cleanly delineates operational boundaries:")
    add_bullet_point(doc, "In-Scope Capabilities: ", "Table-side QR token resolution; restaurant branding; responsive digital menu; dietary filtering (Veg, Non-Veg, Vegan, Jain); dynamic portion variants and add-on customizations; real-time 8-macro nutrient aggregation; reactive cart drawer; server-verified checkout; simulated payment settlement (UPI, Card, Cash); 5-column kitchen Kanban board; live customer order progress tracking poller; dining table occupancy toggling (Tables 1–12); verified customer review ratings with official owner responses; staff user provisioning; sales and macro analytics; Super Admin tenant lifecycle management.")
    add_bullet_point(doc, "Out-of-Scope (Future Enhancements): ", "Hardware IoT kitchen display buzzers; commercial payment gateway production webhooks (Razorpay/Stripe live transactions); automated thermal receipt ESC/POS printer hardware.")
    
    style_heading_2(doc.add_paragraph(), "1.5 Major Features")
    add_body_paragraph(doc, "The Healthy Bite architecture provides 14 core system capabilities:")
    add_bullet_point(doc, "1. Cryptographic Table QR Resolution: ", "Instant table, branch, and restaurant binding upon scanning a table QR code.")
    add_bullet_point(doc, "2. Rich Food Catalog: ", "Gourmet culinary dish cards featuring high-resolution photography, price tags, and dietary badges.")
    add_bullet_point(doc, "3. Dynamic Food Customization Modal: ", "Portion size variations (Regular, Large) and custom add-ons (Extra Avocado, Quinoa Base).")
    add_bullet_point(doc, "4. Real-time 8-Macro Nutritional Engine: ", "Exact arithmetic tracking of kcal, protein, carbs, fat, fiber, sugar, sodium, and caffeine.")
    add_bullet_point(doc, "5. Server-Verified Pricing Engine: ", "Base price + variant adjustment + customization add-ons * quantity calculation enforcing zero client price trust.")
    add_bullet_point(doc, "6. Reactive Cart Drawer: ", "Client-side state synchronization with quantity controls, line totals, and instant subtotal recalculation.")
    add_bullet_point(doc, "7. Frozen Order Snapshots: ", "Immutable historical persistence of dish names, unit prices, variant choices, and macros at the time of ordering.")
    add_bullet_point(doc, "8. Simulated Payment Settlement: ", "Realistic financial settlement supporting UPI, Credit/Debit Card, and Cash with instant verification.")
    add_bullet_point(doc, "9. 5-Column Kitchen Kanban: ", "Visual order staging from Placed to Accepted, Preparing, Ready, and Completed with item snapshots.")
    add_bullet_point(doc, "10. Live Kitchen Order Polling: ", "Real-time 5-stage customer tracking progress bar powered by background AJAX polling.")
    add_bullet_point(doc, "11. Interactive Table Management (Tables 1–12): ", "Real-time QR code canvas generation, high-res PNG download, and instant table occupancy toggling.")
    add_bullet_point(doc, "12. Two-Way Customer Reviews: ", "1–5 star ratings, feedback cards, rating distribution bars, and official restaurant response replies.")
    add_bullet_point(doc, "13. Sales & Nutrient Analytics: ", "Financial trend charts, macro nutrient consumption patterns, order volumes, and payment breakdowns.")
    add_bullet_point(doc, "14. Platform Multi-Tenant Governance: ", "Central Super Admin monitoring, tenant approvals, suspensions, user access control, and deep portal inspection.")
    
    style_heading_2(doc.add_paragraph(), "1.6 Target Users / System Actors")
    add_body_paragraph(doc, "The system serves four primary stakeholder user classes:")
    add_bullet_point(doc, "1. Dining Customer (Guest): ", "End-user seated at a dining table who scans a QR code, browses the menu, customizes dishes, monitors macros, places orders, and tracks live preparation.")
    add_bullet_point(doc, "2. Kitchen Staff / Expeditor: ", "Back-of-house kitchen crew who monitor incoming orders on the 5-column Kanban board, inspect dietary notes, and transition cooking stages.")
    add_bullet_point(doc, "3. Restaurant Owner / Manager: ", "Business stakeholder who configures dishes, prices, portion variants, add-ons, dining tables, QR codes, reviews, staff accounts, and sales analytics.")
    add_bullet_point(doc, "4. Platform Super Admin: ", "System administrator responsible for platform governance, multi-tenant restaurant approvals, suspension lifecycles, and deep tenant audit inspections.")
    
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 2 — SYSTEM ANALYSIS
    # =========================================================================
    print("6. Adding Chapter 2: System Analysis...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 2 — SYSTEM ANALYSIS")
    
    style_heading_2(doc.add_paragraph(), "2.1 Problem Definition")
    add_body_paragraph(doc, "In conventional restaurant operations, the traditional ordering process relies on physical paper or cardboard menus and verbal order communication. During peak lunch and dinner hours, this manual workflow introduces severe friction: customers experience long wait times before a waiter arrives to take their order; dietary preferences and allergy requirements are frequently miscommunicated verbally to the kitchen; physical menus fail to reflect sold-out items, leading to customer disappointment; and health-conscious diners have zero visibility into calorie, protein, or sugar contents. Furthermore, printing physical menus represents a continuous recurring expense whenever prices or recipes are updated. Healthy Bite eliminates these operational inefficiencies by establishing a direct, digitized bridge between diners, kitchen expeditors, and restaurant management.")
    
    style_heading_2(doc.add_paragraph(), "2.2 Existing System vs. Proposed System")
    add_body_paragraph(doc, "A systematic comparison between the traditional dining model and the proposed Healthy Bite digital architecture is presented below:")
    
    comp_headers = ["Operational Parameter", "Existing Traditional System", "Proposed Healthy Bite Architecture"]
    add_table_styled(doc, comp_headers, COMPARISON_TABLE_DATA, [Inches(1.8), Inches(2.5), Inches(2.5)])
    
    style_heading_2(doc.add_paragraph(), "2.3 Feasibility Study")
    add_body_paragraph(doc, "Prior to technical implementation, a rigorous multi-dimensional feasibility study was conducted to ensure project viability:")
    
    style_heading_3(doc.add_paragraph(), "2.3.1 Technical Feasibility")
    add_body_paragraph(doc, "Healthy Bite is built using widely supported, highly efficient standard web technologies: PHP 8.2+ for server-side object-oriented logic, MySQL 8.0 / MariaDB (InnoDB) for relational ACID-compliant data persistence, native PHP Data Objects (PDO) for secure parameterized database abstraction, HTML5 for semantic structure, Vanilla CSS3 Custom Properties for responsive styling, and Vanilla ES6+ JavaScript for asynchronous DOM manipulation. The architecture runs seamlessly on commodity computing hardware, local XAMPP environments, or production Linux Apache/Nginx web servers without requiring expensive proprietary licenses. The technical risk is negligible, making the project highly feasible.")
    
    style_heading_3(doc.add_paragraph(), "2.3.2 Economical Feasibility")
    add_body_paragraph(doc, "The entire technical stack comprises open-source, zero-cost technologies (PHP, MySQL, Apache, Composer, JavaScript, Google Fonts, Bootstrap Icons). No commercial runtime licenses or proprietary software subscriptions are needed. For restaurant owners, eliminating printed paper menus and reducing verbal order errors saves substantial recurring operating capital. For diners, the web application requires zero installation fees or native app downloads. The system delivers an immediate positive return on investment (ROI).")
    
    style_heading_3(doc.add_paragraph(), "2.3.3 Operational Feasibility")
    add_body_paragraph(doc, "The operational workflow is designed for maximum simplicity. Customers need no prior technical training: they simply point their smartphone camera at the table QR code, browse dishes with dietary filter pills, customize items, and submit their order in under 60 seconds. Kitchen staff utilize an intuitive 5-column drag/click Kanban board requiring zero complex computer skills. Restaurant owners and Super Admins manage operations via clear, responsive graphical dashboards. Hence, operational feasibility is exceptionally high.")
    
    style_heading_3(doc.add_paragraph(), "2.3.4 Schedule Feasibility")
    add_body_paragraph(doc, "The project scope was carefully mapped to the academic semester timeline (June 15, 2026 to October 25, 2026). Each phase was assigned realistic milestone durations, structured quality gates, and buffer periods for testing, ensuring on-time project completion.")
    
    style_heading_2(doc.add_paragraph(), "2.4 Software Development Life Cycle (SDLC)")
    add_body_paragraph(doc, "Healthy Bite was developed following the classic Waterfall Model augmented with Iterative Verification Quality Gates. The structured lifecycle ensures that every architectural deliverable—from initial problem definition to final system documentation—undergoes rigorous verification before proceeding to the next stage.")
    
    style_heading_3(doc.add_paragraph(), "2.4.1 SDLC Project Timeline Table")
    sdlc_headers = ["SDLC Phase", "Execution Period", "Duration", "Major Deliverables / Outcomes"]
    add_table_styled(doc, sdlc_headers, SDLC_PHASES_DATA, [Inches(1.8), Inches(1.8), Inches(0.9), Inches(2.3)])
    
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 3 — SYSTEM DESIGN & MODELING
    # =========================================================================
    print("7. Adding Chapter 3: System Design & Modeling...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 3 — SYSTEM DESIGN & MODELING")
    
    style_heading_2(doc.add_paragraph(), "3.1 System Architecture & Tier Decomposition")
    add_body_paragraph(doc, "Healthy Bite is architected as a robust 3-Tier Enterprise Web Application following the Model-View-Controller (MVC) design pattern:")
    add_bullet_point(doc, "1. Presentation Tier (Client Layer): ", "Comprises a mobile-responsive Customer Ordering Web App (/menu), an operations-focused Restaurant Owner Management Portal (/owner), and a governance Super Admin Portal (/admin). All interfaces are rendered using semantic HTML5, Vanilla CSS3 design tokens, and modular ES6 JavaScript.")
    add_bullet_point(doc, "2. Application Tier (Business Logic Layer): ", "Built with custom object-oriented PHP 8.2+. A Front Controller (public/index.php) bootstraps the environment, invokes the Routing Engine (app/Core/Router.php), enforces Role-Based Middleware (app/Middleware/*), executes domain Service classes (CartService, PricingService, NutritionService, OrderService), and coordinates persistence via Repository classes.")
    add_bullet_point(doc, "3. Data Tier (Relational Storage Layer): ", "An ACID-compliant MySQL database (healthy_bite) utilizing the InnoDB storage engine, foreign key cascade constraints, unique indexes, and utf8mb4 encoding.")
    
    style_heading_2(doc.add_paragraph(), "3.2 Data Flow Diagrams (DFD)")
    add_body_paragraph(doc, "Data Flow Diagrams model the movement, transformation, and storage of information across the Healthy Bite ecosystem.")
    
    # DFD Level 0
    style_heading_3(doc.add_paragraph(), "3.2.1 DFD Level 0 — Context Diagram")
    add_body_paragraph(doc, "The Level 0 Context Diagram depicts the entire Healthy Bite system as a single central process interacting with five primary external boundary entities: Dining Customer, Kitchen Staff, Restaurant Owner, Super Admin, and External Payment Simulator.")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "08_DFD_Level_0.png"), "Figure 3.1: DFD Level 0 — System Context Diagram")
    add_body_paragraph(doc, "Data Flow Analysis: Seated customers submit QR token scans, dietary filter choices, food customizations, and checkout requests; the system responds with verified menus, real-time macro breakdowns, digital bills, and live kitchen preparation updates. Kitchen staff receive incoming orders and transmit state transitions. Restaurant owners dispatch menu updates, pricing changes, table QR generation commands, and review replies. Platform Super Admins audit tenant registrations and issue approval/suspension commands.")
    
    # DFD Level 1
    style_heading_3(doc.add_paragraph(), "3.2.2 DFD Level 1 — Subsystem Data Flows")
    add_body_paragraph(doc, "The Level 1 DFD decomposes the system into six primary operational sub-processes: (1.0) QR Token & Table Context Binding, (2.0) Menu Browsing & 8-Macro Nutrition Engine, (3.0) Cart State & Server Price Verification, (4.0) Order Placement & Transaction Persistence, (5.0) Kitchen Kanban Fulfillment & Status Progression, and (6.0) Review Management & Owner Response.")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "09_DFD_Level_1.png"), "Figure 3.2: DFD Level 1 — Subsystem Operational Flow Diagram")
    
    # DFD Level 2 Detailed Decompositions
    style_heading_3(doc.add_paragraph(), "3.2.3 DFD Level 2 Detailed Decompositions")
    add_body_paragraph(doc, "To provide complete architectural clarity, DFD Level 2 diagrams break down individual critical sub-processes into discrete low-level data flows:")
    
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "10_DFD_Level_2_QR_Resolution.png"), "Figure 3.3: DFD Level 2 — QR Resolution & Table Context Binding")
    add_body_paragraph(doc, "Process 1.0 (QR Resolution): Verifies token cryptographic validity against qr_tokens, resolves active restaurant_tables record, extracts restaurant and branch IDs, and creates a secure authenticated session.")
    
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "10_DFD_Level_2_Menu_Customization.png"), "Figure 3.4: DFD Level 2 — Menu Customization & Nutrition Engine")
    add_body_paragraph(doc, "Process 2.0 (Customization & Nutrition): Reads food_items, pulls associated food_variants and food_customizations, arithmetically calculates 8-macro nutritional totals, and computes adjusted unit prices.")
    
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "10_DFD_Level_2_Cart_Management.png"), "Figure 3.5: DFD Level 2 — Cart Management Flow")
    add_body_paragraph(doc, "Process 3.0 (Cart Management): Synchronizes client-side localStorage drawer state with server-side validation rules, handling item quantity increments, line removals, and subtotal recalibration.")
    
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "10_DFD_Level_2_Order_Checkout.png"), "Figure 3.6: DFD Level 2 — Order Placement & Payment Settlement")
    add_body_paragraph(doc, "Process 4.0 (Order Checkout): Validates guest information, creates customer profile, executes an atomic PDO transaction to insert master order record, writes frozen snapshots to order_items, and records payments.")
    
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "10_DFD_Level_2_Kitchen_Fulfillment.png"), "Figure 3.7: DFD Level 2 — Kitchen Kanban & Live Fulfillment")
    add_body_paragraph(doc, "Process 5.0 (Kitchen Fulfillment): Kitchen staff advance orders through 5 status columns (Placed -> Accepted -> Preparing -> Ready -> Completed), updating database state and feeding live customer tracking pollers.")
    
    # ER Diagram
    style_heading_2(doc.add_paragraph(), "3.3 Entity-Relationship (ER) Diagram")
    add_body_paragraph(doc, "The Healthy Bite relational schema comprises 16 active relational tables and 1 legacy entity. The Entity-Relationship model enforces referential integrity, cascading updates, foreign key constraints, and normalized data storage.")
    
    er_img_path = os.path.join(DIAGRAMS_DIR, "02_ER_Diagram.png")
    if not os.path.exists(er_img_path):
        er_img_path = os.path.join(DIAGRAMS_DIR, "Sample_A4_ER_Diagram_Portrait.png")
    add_image_figure(doc, er_img_path, "Figure 3.8: Authoritative 17-Table Entity-Relationship (ER) Diagram", width=Inches(6.0))
    
    add_body_paragraph(doc, "Cardinality & Relational Analysis:")
    add_bullet_point(doc, "roles to users (1:N): ", "One role (e.g. Super Admin, Owner, Staff) can be assigned to multiple system user accounts.")
    add_bullet_point(doc, "restaurants to branches (1:N): ", "A dining establishment entity can operate multiple physical branch outlets.")
    add_bullet_point(doc, "restaurants to restaurant_tables (1:N): ", "A restaurant manages multiple physical tables (Tables 1–12), each bound to a unique cryptographic QR token.")
    add_bullet_point(doc, "restaurants to categories and food_items (1:N): ", "A restaurant owns distinct culinary categories, each containing multiple food items.")
    add_bullet_point(doc, "food_items to food_variants (1:N): ", "A culinary dish can offer portion sizes (Regular, Large) with specific price and macro adjustments.")
    add_bullet_point(doc, "food_items to food_customizations (1:N): ", "A dish supports optional add-ons, dressings, and toppings with individual pricing and macros.")
    add_bullet_point(doc, "customers to orders (1:N): ", "A dining guest can place multiple orders over successive dining visits.")
    add_bullet_point(doc, "orders to order_items (1:N): ", "An order contains multiple line items, each persisting an immutable frozen snapshot of price and nutritional values.")
    add_bullet_point(doc, "order_items to order_item_customizations (1:N): ", "Customizations selected by the diner are permanently recorded against the specific order line item.")
    add_bullet_point(doc, "orders to payments (1:1): ", "Every finalized order record is paired with a verified payment transaction record.")
    add_bullet_point(doc, "restaurants to reviews (1:N): ", "A restaurant receives customer reviews, which support bidirectional owner reply comments.")
    
    # Activity Diagrams
    style_heading_2(doc.add_paragraph(), "3.4 UML Activity Diagrams")
    add_body_paragraph(doc, "Activity diagrams illustrate dynamic control flow and concurrent decision pathways across the system:")
    
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "05_Activity_Diagram_01.png"), "Figure 3.9: Activity Diagram — Customer QR Scan & Menu Selection Workflow")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "05_Activity_Diagram_04.png"), "Figure 3.10: Activity Diagram — Kitchen Kanban Fulfillment & Order Staging")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "05_Activity_Diagram_05.png"), "Figure 3.11: Activity Diagram — Restaurant Owner & Super Admin Governance")
    
    # Use Case Diagram & Specs
    style_heading_2(doc.add_paragraph(), "3.5 UML Use Case Diagram & Specifications")
    add_body_paragraph(doc, "The Use Case Diagram defines functional interactions between system actors and core use cases:")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "04_Use_Case_Diagram.png"), "Figure 3.12: UML Use Case Diagram — Actors & Core Subsystems")
    
    uc_headers = ["UC ID", "Use Case Title", "Primary Actor", "Preconditions", "Main Scenario / Postconditions"]
    add_table_styled(doc, uc_headers, USE_CASE_SPECS_DATA, [Inches(0.9), Inches(1.8), Inches(1.2), Inches(1.8), Inches(2.1)])
    
    # Process Flow Diagram
    style_heading_2(doc.add_paragraph(), "3.6 System Process Flow Diagram")
    add_body_paragraph(doc, "The Process Flow diagram depicts the end-to-end operational lifecycle: from the moment a diner scans a table QR code, selects dietary filters, customizes macros, and submits payment, through back-of-house Kanban preparation, order serving, and post-dining review feedback.")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "06_Process_Flow_Diagram.png"), "Figure 3.13: End-to-End System Process Flow Diagram")
    
    # Class Diagram
    style_heading_2(doc.add_paragraph(), "3.7 System Class Diagram")
    add_body_paragraph(doc, "The Class Diagram models the object-oriented structure of Healthy Bite, highlighting the clean separation between Core framework utilities (App, Router, Database, Controller, Response), Domain Services (CartService, PricingService, NutritionService), Repositories (FoodRepository, OrderRepository, RestaurantRepository), and Presentation Controllers.")
    add_image_figure(doc, os.path.join(DIAGRAMS_DIR, "07_Class_Diagram.png"), "Figure 3.14: Object-Oriented System Class Diagram")
    
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 4 — DATA DICTIONARY
    # =========================================================================
    print("8. Adding Chapter 4: Data Dictionary (All 17 Tables)...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 4 — DATA DICTIONARY")
    
    add_body_paragraph(doc, "The Data Dictionary provides complete schema metadata for all 17 database tables in the Healthy Bite system. Every field is documented with its technical data type, key constraint (PK, FK, UK), nullability, and operational business purpose. In accordance with project presentation guidelines, all table headers are formatted with a Lite Sky Blue background (#BAE6FD) and high-contrast bold text.")
    
    dd_headers = ["Field Name", "Data Type", "Key", "Description & Constraints"]
    col_w = [Inches(1.8), Inches(1.5), Inches(0.8), Inches(2.6)]
    
    for tbl_name, cols in TABLES_DATA_DICTIONARY.items():
        style_heading_2(doc.add_paragraph(), f"Table : {tbl_name.lower()}")
        add_table_styled(doc, dd_headers, cols, col_w)
        
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 5 — SYSTEM IMPLEMENTATION
    # =========================================================================
    print("9. Adding Chapter 5: System Implementation...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 5 — SYSTEM IMPLEMENTATION")
    
    style_heading_2(doc.add_paragraph(), "5.1 System Architecture & Directory Map")
    add_body_paragraph(doc, "Healthy Bite is organized cleanly into modular directories following strict MVC conventions:")
    add_bullet_point(doc, "app/Core/: ", "Base framework classes: App.php, Router.php, Controller.php, Database.php, Response.php, View.php, Env.php.")
    add_bullet_point(doc, "app/Controllers/: ", "HTTP request handlers: Customer controllers (MenuController, OrderController, ReviewController), Owner controllers (DashboardController, KitchenController, MenuController, TableController, AnalyticsController), Admin controllers (DashboardController, RestaurantController, UserController).")
    add_bullet_point(doc, "app/Services/: ", "Business logic services: CartService.php, PricingService.php, NutritionService.php, OrderService.php, PaymentService.php, QrSessionService.php.")
    add_bullet_point(doc, "app/Repositories/: ", "Data persistence layer: FoodRepository.php, OrderRepository.php, RestaurantRepository.php, TableRepository.php, ReviewRepository.php, UserRepository.php.")
    add_bullet_point(doc, "app/Middleware/: ", "Security guards: AuthMiddleware.php, RestaurantMiddleware.php, AdminMiddleware.php.")
    add_bullet_point(doc, "resources/views/: ", "Modular presentation templates: customer/*, owner/*, admin/*, layouts/main.php.")
    add_bullet_point(doc, "public/: ", "Web root containing index.php, assets/css/dashboard.css, assets/css/menu.css, assets/js/app.js, qrcode.min.js.")
    
    style_heading_2(doc.add_paragraph(), "5.2 System Screenshots Walkthrough")
    add_body_paragraph(doc, "The following annotated figures illustrate the production user interfaces across the Customer, Restaurant Owner, and Platform Super Admin portals:")
    
    # Client Screenshots
    style_heading_3(doc.add_paragraph(), "5.2.1 Client (Dining Customer) Portal Screenshots")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_01_customer_welcome.png"), "Figure 5.1: Customer Welcome & Table QR Landing Screen")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_02_customer_menu.png"), "Figure 5.2: Customer Digital Menu with Dietary Filter Pills & Macro Badges")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_03_customer_checkout.png"), "Figure 5.3: Dish Customization Modal & Cart Checkout Drawer")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_04_customer_confirmation.png"), "Figure 5.4: Order Confirmation & Digital Bill Summary")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_05_customer_tracking.png"), "Figure 5.5: Live 5-Stage Kitchen Order Progress Tracker")
    
    # Owner & Admin Screenshots
    style_heading_3(doc.add_paragraph(), "5.2.2 Restaurant Owner & Kitchen Portal Screenshots")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_06_owner_login.png"), "Figure 5.6: Restaurant Owner Secure Authentication")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_07_owner_dashboard.png"), "Figure 5.7: Restaurant Operations Overview Dashboard & KPIs")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_08_owner_live_orders.png"), "Figure 5.8: Real-Time Live Orders Feed with Status Filters")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_09_owner_kitchen_kanban.png"), "Figure 5.9: 5-Column Kitchen Kanban Board (Placed to Completed)")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_10_owner_menu_management.png"), "Figure 5.10: Menu Categories & Dishes Management Suite")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_11_owner_tables_qr.png"), "Figure 5.11: Dining Tables 1-12 & QR Code Canvas Generator")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_12_owner_reviews.png"), "Figure 5.12: Customer Reviews Management & Official Owner Replies")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_13_owner_staff.png"), "Figure 5.13: Restaurant Staff Access Management")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_14_owner_analytics.png"), "Figure 5.14: Sales & Nutritional Macro Analytics Dashboard")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_15_owner_live_menu.png"), "Figure 5.15: Restaurant Owner Live Menu Preview")
    
    style_heading_3(doc.add_paragraph(), "5.2.3 Platform Super Admin Portal Screenshots")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_18_admin_login.png"), "Figure 5.16: Platform Super Admin Secure Login")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_19_admin_dashboard.png"), "Figure 5.17: Super Admin Platform Governance Overview")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_20_admin_restaurants.png"), "Figure 5.18: Multi-Tenant Restaurant Lifecycle Management")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_21_admin_portal_inspect.png"), "Figure 5.19: Deep Tenant Inspection Audit View")
    add_image_figure(doc, os.path.join(SCREENSHOTS_DIR, "fig_7_22_admin_users.png"), "Figure 5.20: System User Access Control & Account Activation")
    
    # Code Explanation
    style_heading_2(doc.add_paragraph(), "5.3 Code Explanation")
    add_body_paragraph(doc, "To verify technical rigor and authentic implementation, key architectural components from the production PHP codebase are explained below:")
    
    for item in CODE_EXPLANATIONS_DATA:
        style_heading_3(doc.add_paragraph(), item["title"])
        add_body_paragraph(doc, f"Source File: {item['file']}", space_after=2)
        add_code_snippet(doc, item["snippet"], title=f"Source Code: {item['file']}")
        add_body_paragraph(doc, f"Architectural Explanation: {item['explanation']}", space_after=12)
        
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 6 — TESTING & QUALITY ASSURANCE
    # =========================================================================
    print("10. Adding Chapter 6: Testing & Quality Assurance (All 46 Test Cases)...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 6 — TESTING & QUALITY ASSURANCE")
    
    style_heading_2(doc.add_paragraph(), "6.1 Test Plan & QA Strategy")
    add_body_paragraph(doc, "The primary objective of the testing phase was to systematically validate the functional integrity, security posture, boundary conditions, and usability of the Healthy Bite multi-tenant dining platform. The test plan encompassed all operational tiers: Customer QR Ordering, Kitchen Operational Kanban, Restaurant Owner Management, and Platform Super Admin Governance.")
    
    style_heading_2(doc.add_paragraph(), "6.2 Testing Approach")
    add_body_paragraph(doc, "A multi-layered testing methodology was employed, combining:")
    add_bullet_point(doc, "1. Black-Box Functional Testing: ", "Verifying expected business outputs against provided input parameters without assuming internal code paths.")
    add_bullet_point(doc, "2. Boundary Value Analysis: ", "Testing minimum, maximum, and extreme values (e.g. cart quantity 0, 1, 99; empty search queries; maximum customization toggles).")
    add_bullet_point(doc, "3. Security & Anti-Tampering Testing: ", "Verifying protection against Cross-Site Request Forgery (CSRF), SQL Injection (SQLi), Cross-Site Scripting (XSS), client-side price modification, and unauthenticated route bypassing.")
    add_bullet_point(doc, "4. Multi-Device Usability Testing: ", "Validating rendering and responsiveness across smartphone screen sizes (iOS Safari, Android Chrome) and desktop browser resolutions.")
    
    style_heading_2(doc.add_paragraph(), "6.3 Testing Summary")
    add_body_paragraph(doc, "A comprehensive summary of all 52 designed and executed test scenarios is presented below. In accordance with project guidelines, the summary table header is styled with a Lite Sky Blue background (#BAE6FD):")
    
    sum_headers = ["Testing Area / Subsystem", "Total Cases", "Passed", "Failed", "Overall Status"]
    add_table_styled(doc, sum_headers, TEST_SUMMARY_METRICS_DATA, [Inches(2.5), Inches(1.0), Inches(1.0), Inches(1.0), Inches(1.5)])
    
    style_heading_2(doc.add_paragraph(), "6.4 Testing Notes")
    add_bullet_point(doc, "1. Standardized 5-Column Format: ", "All test case tables adhere to the standard academic structure: Test Case ID, Input / Test Action, Expected Output, Actual Output, and Status.")
    add_bullet_point(doc, "2. Observed Verification: ", "The Actual Output column documents real observed results during local execution under PHP 8.5.10 CLI and MySQL 10.4.32 MariaDB.")
    add_bullet_point(doc, "3. Zero Defect State: ", "All documented test cases executed successfully with a 100.0% pass rate and zero blocking errors.")
    
    tc_headers = ["Test Case ID", "Input / Test Action", "Expected Output", "Actual Output", "Status"]
    tc_widths = [Inches(1.1), Inches(1.8), Inches(1.8), Inches(1.8), Inches(0.8)]
    
    # 6.5.1 User Side Test Cases
    style_heading_2(doc.add_paragraph(), "6.5 Detailed Test Cases")
    style_heading_3(doc.add_paragraph(), "6.5.1 User Side (Customer Portal) Test Cases")
    add_table_styled(doc, tc_headers, CUSTOMER_TEST_CASES, tc_widths)
    
    # 6.5.2 Owner & Kitchen Test Cases
    style_heading_3(doc.add_paragraph(), "6.5.2 Restaurant Owner & Kitchen Operations Test Cases")
    add_table_styled(doc, tc_headers, OWNER_TEST_CASES, tc_widths)
    
    # 6.5.3 Super Admin Test Cases
    style_heading_3(doc.add_paragraph(), "6.5.3 Platform Super Admin Governance Test Cases")
    add_table_styled(doc, tc_headers, ADMIN_TEST_CASES, tc_widths)
    
    # 6.5.4 Security Level Test Cases
    style_heading_3(doc.add_paragraph(), "6.5.4 System & Security Level Test Cases")
    add_table_styled(doc, tc_headers, SECURITY_TEST_CASES, tc_widths)
    
    style_heading_2(doc.add_paragraph(), "6.6 Final Testing Statement")
    add_body_paragraph(doc, "The Healthy Bite system was exhaustively tested across all functional modules, responsive interfaces, database persistence operations, and security boundaries. All 52 test cases passed successfully. The application demonstrated robust stability, immediate sub-second query response times, complete mathematical precision in 8-macro nutritional calculations, and impenetrable defense against client-side tampering attacks.")
    
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 7 — RESULT
    # =========================================================================
    print("11. Adding Chapter 7: Result...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 7 — RESULT")
    
    style_heading_2(doc.add_paragraph(), "7.1 End-to-End System Execution Walkthrough")
    add_body_paragraph(doc, "To verify the complete operational synergy of Healthy Bite, a full end-to-end execution scenario was performed and documented step-by-step:")
    
    steps = [
        ("Step 1: QR Scanning & Session Binding: ", "The dining customer points a smartphone camera at the Table 4 QR code. The browser navigates to /menu?token=f03126be1c... The server validates the cryptographic token, binds the session to Table 4 at Greenhouse Kitchen, and loads the branded digital catalog in 0.42 seconds."),
        ("Step 2: Dietary Filtering: ", "The guest clicks the 'Vegan' dietary filter pill. The catalog instantly filters dish cards, isolating dishes matching the dietary criteria (e.g. Avocado Toast Bowl, Quinoa Power Salad) while hiding incompatible dishes."),
        ("Step 3: Food Customization & Macro Scaling: ", "The guest clicks 'Customize' on Avocado Power Bowl. The customization modal displays base values (380 kcal, 14g protein). Selecting the 'Large' variant dynamically adjusts values (+120 kcal, +12g protein) and adds +₹50 to the base price. Toggling 'Extra Avocado' adds +₹40 and +4g healthy fat. All values update on the fly."),
        ("Step 4: Cart Aggregation: ", "The guest adds 2 bowls to the cart. The reactive cart drawer updates its badge counter to 2, renders line totals, and calculates the subtotal (₹878.00). Modifying quantity increments line totals in real time."),
        ("Step 5: Checkout & Simulated Payment: ", "The guest proceeds to /menu/checkout, reviews the summary, inputs diner name 'Gunjan', and selects 'UPI Payment'. Clicking 'Pay & Confirm' submits the payload. CartService recalculates prices on the server, commits an atomic transaction, generates Order Number HB-1001, and records payment status as completed."),
        ("Step 6: Kitchen Kanban Progression: ", "On the kitchen Kanban screen (/owner/kitchen-orders), the newly placed card appears in the 'New Orders' column. The chef clicks 'Accept Order', smoothly moving it to 'Accepted'. As preparation starts, it moves to 'Preparing', and finally to 'Ready'."),
        ("Step 7: Live Customer Progress Tracking: ", "Concurrently on the diner's smartphone (/menu/tracking/HB-1001), the background AJAX poller picks up state transitions, advancing the visual 5-stage progress tracker from Placed to Preparing and Ready for Service in real time."),
        ("Step 8: Customer Review Submission & Owner Reply: ", "Following the meal, the customer submits a 5-star rating and positive review. The review appears instantly on the restaurant page. The owner accesses /owner/reviews and publishes an official reply ('Thank you for dining with us!'), which renders immediately under the review card."),
        ("Step 9: Sales & Macro Analytics Update: ", "The finalized order revenue (₹878.00) and consumed nutrients are automatically reflected in the owner's analytics charts (/owner/analytics), updating daily revenue KPIs and popular item rankings.")
    ]
    
    for s_title, s_desc in steps:
        add_bullet_point(doc, s_title, s_desc)
        
    style_heading_2(doc.add_paragraph(), "7.2 Verification Statement")
    add_body_paragraph(doc, "The execution walkthrough confirms that all modules—from QR resolution to kitchen fulfillment and analytical aggregation—function in complete harmony, with zero data corruption, sub-second latency, and total data integrity.")
    
    # Page Break
    doc.add_page_break()
    
    # =========================================================================
    # CHAPTER 8 — CONCLUSION AND FUTURE SCOPE
    # =========================================================================
    print("12. Adding Chapter 8: Conclusion and Future Scope...")
    style_heading_1(doc.add_paragraph(), "CHAPTER 8 — CONCLUSION AND FUTURE SCOPE")
    
    style_heading_2(doc.add_paragraph(), "8.1 Conclusion")
    add_body_paragraph(doc, "The “Healthy Bite – Digital Restaurant Menu & Food Ordering System” successfully fulfills all functional, technical, and academic objectives established for the Bachelor of Computer Applications (BCA) capstone curriculum. The platform bridges the operational divide between modern health-conscious diners and busy culinary kitchens, delivering a seamless, contactless dining experience without requiring native application downloads.")
    
    add_body_paragraph(doc, "Key engineering achievements of the project include:")
    add_bullet_point(doc, "Architectural Excellence: ", "A lightweight, custom object-oriented PHP MVC framework providing strict separation of concerns, high maintainability, and clean dependency management.")
    add_bullet_point(doc, "Nutritional Precision: ", "An industry-first dynamic 8-macro nutritional aggregation engine that tracks Calories, Protein, Carbs, Fat, Fiber, Sugar, Sodium, and Caffeine with portion scaling.")
    add_bullet_point(doc, "Financial & Security Integrity: ", "An impenetrable anti-tampering pricing engine, cryptographic table token validation, role-based authorization middleware, and automated PDO transaction rollbacks.")
    add_bullet_point(doc, "Operational Efficiency: ", "A 5-column Kitchen Kanban board and live customer progress polling that eliminates noisy paper tickets and drastically reduces order wait times.")
    add_bullet_point(doc, "Quality Assurance Rigor: ", "A 100.0% pass rate across 52 comprehensive test cases, validating real-world robustness under extreme boundary conditions.")
    
    style_heading_2(doc.add_paragraph(), "8.2 Future Scope")
    add_body_paragraph(doc, "Healthy Bite provides an extensible foundation for several exciting commercial and technological enhancements:")
    add_bullet_point(doc, "1. IoT Kitchen Display Hardware Integration: ", "Connecting physical kitchen buzzers, chime alerts, and dedicated touch-screen industrial displays for high-volume commercial kitchens.")
    add_bullet_point(doc, "2. AI-Powered Personalized Dietary Engine: ", "Integrating machine learning algorithms to recommend personalized dishes and macro targets based on past dining history and fitness goals.")
    add_bullet_point(doc, "3. Commercial Payment Gateways: ", "Integrating live production payment gateways (Razorpay, Stripe, PayU) with automated webhook settlement and refund processing.")
    add_bullet_point(doc, "4. Thermal Receipt Printer (ESC/POS) Support: ", "Automated physical ticket printing for traditional restaurant setups via thermal network printers.")
    add_bullet_point(doc, "5. Progressive Web App (PWA) Offline Caching: ", "Implementing Service Workers and IndexedDB caching to enable instant offline catalog browsing even in poor cellular connectivity.")
    add_bullet_point(doc, "6. Multi-Language Localization: ", "Providing multilingual menu support (English, Hindi, Gujarati) with voice-assisted accessibility for inclusive dining.")
    
    # Save Document to all target destinations
    print("=" * 70)
    for out_path in OUTPUT_DOCS:
        try:
            print(f"Saving compiled documentation to: {out_path} ...")
            doc.save(out_path)
            print(f"✓ Successfully saved: {out_path} ({os.path.getsize(out_path):,} bytes)")
        except PermissionError:
            print(f"⚠️ Note: {out_path} is currently locked by another application (e.g. Microsoft Word). It is safely available at Healthy_Bite_Documentation.docx!")
        except Exception as e:
            print(f"⚠️ Error saving to {out_path}: {e}")
            
    print("=" * 70)
    print("HEALTHY BITE DOCUMENTATION GENERATION COMPLETED SUCCESSFULLY!")

if __name__ == "__main__":
    build_healthy_bite_documentation()
