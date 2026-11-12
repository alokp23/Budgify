"""
Generate Budgify_Presentation.pptx using python-pptx.

Usage:
    pip install python-pptx
    python generate_presentation.py
"""

from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
from pptx.enum.shapes import MSO_SHAPE


# Color Palette
NAVY = RGBColor(30, 41, 59)       # #1E293B
INDIGO = RGBColor(79, 70, 229)    # #4F46E5
TEAL = RGBColor(14, 165, 233)     # #0EA5E9
SLATE = RGBColor(71, 85, 105)     # #475569
LIGHT_BG = RGBColor(248, 250, 252)
WHITE = RGBColor(255, 255, 255)
BORDER = RGBColor(226, 232, 240)
GREEN = RGBColor(16, 185, 129)


def set_slide_background(slide, color):
    bg = slide.background
    fill = bg.fill
    fill.solid()
    fill.fore_color.rgb = color


def add_header(slide, title_text, slide_num):
    # Top accent strip
    strip = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0), Inches(0), Inches(13.333), Inches(0.12))
    strip.fill.solid()
    strip.fill.fore_color.rgb = INDIGO
    strip.line.fill.background()

    # Slide Title
    title_box = slide.shapes.add_textbox(Inches(0.7), Inches(0.35), Inches(10.5), Inches(0.7))
    tf = title_box.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = title_text
    p.font.name = "Calibri"
    p.font.size = Pt(30)
    p.font.bold = True
    p.font.color.rgb = NAVY

    # Divider line
    line = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.7), Inches(1.12), Inches(11.9), Inches(0.03))
    line.fill.solid()
    line.fill.fore_color.rgb = BORDER
    line.line.fill.background()

    # Footer
    footer_box = slide.shapes.add_textbox(Inches(0.7), Inches(7.0), Inches(9.0), Inches(0.35))
    fp = footer_box.text_frame.paragraphs[0]
    fp.text = "Budgify – Personal Expense Tracker | PHP & MySQL Mini Project"
    fp.font.name = "Calibri"
    fp.font.size = Pt(11)
    fp.font.color.rgb = SLATE

    num_box = slide.shapes.add_textbox(Inches(11.5), Inches(7.0), Inches(1.1), Inches(0.35))
    np = num_box.text_frame.paragraphs[0]
    np.alignment = PP_ALIGN.RIGHT
    np.text = f"Slide {slide_num} / 11"
    np.font.name = "Calibri"
    np.font.size = Pt(11)
    np.font.color.rgb = SLATE


def add_bullets(slide, items, left=0.8, top=1.45, width=11.7, height=5.2, font_size=20):
    box = slide.shapes.add_textbox(Inches(left), Inches(top), Inches(width), Inches(height))
    tf = box.text_frame
    tf.word_wrap = True

    for idx, item in enumerate(items):
        p = tf.paragraphs[0] if idx == 0 else tf.add_paragraph()
        p.space_after = Pt(14)
        p.line_spacing = 1.25

        if isinstance(item, tuple):
            lead, rest = item
            r1 = p.add_run()
            r1.text = f"• {lead}: "
            r1.font.name = "Calibri"
            r1.font.size = Pt(font_size)
            r1.font.bold = True
            r1.font.color.rgb = NAVY

            r2 = p.add_run()
            r2.text = rest
            r2.font.name = "Calibri"
            r2.font.size = Pt(font_size)
            r2.font.color.rgb = SLATE
        else:
            r = p.add_run()
            r.text = f"• {item}"
            r.font.name = "Calibri"
            r.font.size = Pt(font_size)
            r.font.color.rgb = SLATE


def add_card(slide, left, top, width, height, title, body_lines, accent=INDIGO):
    card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(left), Inches(top), Inches(width), Inches(height))
    card.fill.solid()
    card.fill.fore_color.rgb = WHITE
    card.line.color.rgb = BORDER
    card.line.width = Pt(1)

    bar = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(left), Inches(top), Inches(width), Inches(0.1))
    bar.fill.solid()
    bar.fill.fore_color.rgb = accent
    bar.line.fill.background()

    tb = slide.shapes.add_textbox(Inches(left + 0.22), Inches(top + 0.22), Inches(width - 0.44), Inches(height - 0.35))
    tf = tb.text_frame
    tf.word_wrap = True

    p0 = tf.paragraphs[0]
    p0.text = title
    p0.font.name = "Calibri"
    p0.font.size = Pt(19)
    p0.font.bold = True
    p0.font.color.rgb = NAVY
    p0.space_after = Pt(8)

    for line in body_lines:
        p = tf.add_paragraph()
        p.text = f"• {line}"
        p.font.name = "Calibri"
        p.font.size = Pt(15)
        p.font.color.rgb = SLATE
        p.space_after = Pt(5)


def build_presentation():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # ------------------------------------------------------------------
    # Slide 1: Title Slide
    # ------------------------------------------------------------------
    s1 = prs.slides.add_slide(blank_layout)
    set_slide_background(s1, NAVY)

    accent_box = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.9), Inches(1.7), Inches(0.14), Inches(3.8))
    accent_box.fill.solid()
    accent_box.fill.fore_color.rgb = TEAL
    accent_box.line.fill.background()

    tb1 = s1.shapes.add_textbox(Inches(1.35), Inches(1.75), Inches(10.8), Inches(4.0))
    tf1 = tb1.text_frame
    tf1.word_wrap = True

    p = tf1.paragraphs[0]
    p.text = "COLLEGE MINI PROJECT PRESENTATION"
    p.font.name = "Calibri"
    p.font.size = Pt(15)
    p.font.bold = True
    p.font.color.rgb = TEAL
    p.space_after = Pt(14)

    p2 = tf1.add_paragraph()
    p2.text = "Budgify – Personal Expense Tracker"
    p2.font.name = "Calibri"
    p2.font.size = Pt(44)
    p2.font.bold = True
    p2.font.color.rgb = WHITE
    p2.space_after = Pt(14)

    p3 = tf1.add_paragraph()
    p3.text = "A Simple, Secure Web Application Built with Core PHP, MySQL (PDO) & Bootstrap 5"
    p3.font.name = "Calibri"
    p3.font.size = Pt(22)
    p3.font.color.rgb = RGBColor(203, 213, 225)
    p3.space_after = Pt(28)

    p4 = tf1.add_paragraph()
    p4.text = "Presented by: [Your Name]  |  Roll No: [Your Roll No]  |  Department of Computer Science"
    p4.font.name = "Calibri"
    p4.font.size = Pt(16)
    p4.font.color.rgb = RGBColor(148, 163, 184)

    # ------------------------------------------------------------------
    # Slide 2: Introduction
    # ------------------------------------------------------------------
    s2 = prs.slides.add_slide(blank_layout)
    set_slide_background(s2, LIGHT_BG)
    add_header(s2, "Introduction", 2)
    add_bullets(s2, [
        ("What is Budgify?", "A web-based Personal Expense Tracker designed to record daily income and expenses."),
        ("Target Users", "College students and individuals who want a simple, clutter-free financial overview."),
        ("Core Idea", "Replace manual notebooks and messy spreadsheets with an organized, password-protected web dashboard."),
        ("Instant Insights", "Automatically calculates Total Income, Total Expenses, Remaining Balance, and Monthly Summaries."),
    ])

    # ------------------------------------------------------------------
    # Slide 3: Problem Statement
    # ------------------------------------------------------------------
    s3 = prs.slides.add_slide(blank_layout)
    set_slide_background(s3, LIGHT_BG)
    add_header(s3, "Problem Statement", 3)
    add_bullets(s3, [
        ("Manual Tracking Errors", "Writing expenses on paper or notes apps makes monthly calculation slow and error-prone."),
        ("Lack of Visibility", "Students often run out of pocket money mid-month without knowing which category took the most."),
        ("Complex Existing Tools", "Many commercial apps have complicated features, ads, or subscription paywalls."),
        ("Need for Privacy", "Users need a private account where only they can view, edit, or delete their financial records."),
    ])

    # ------------------------------------------------------------------
    # Slide 4: Objectives
    # ------------------------------------------------------------------
    s4 = prs.slides.add_slide(blank_layout)
    set_slide_background(s4, LIGHT_BG)
    add_header(s4, "Project Objectives", 4)
    add_bullets(s4, [
        "Build secure user registration, login, and logout using PHP sessions and password hashing.",
        "Implement full CRUD operations (Create, Read, Update, Delete) for income and expenses.",
        "Categorize transactions into Food, Travel, Shopping, Education, Bills, Salary, and Others.",
        "Provide a dynamic dashboard showing Total Income, Total Expenses, and Remaining Balance.",
        "Enable monthly filtering in Reports to review income and spending for any chosen month.",
        "Prevent unauthorized access and SQL injection using PDO prepared statements.",
    ])

    # ------------------------------------------------------------------
    # Slide 5: Technology Stack
    # ------------------------------------------------------------------
    s5 = prs.slides.add_slide(blank_layout)
    set_slide_background(s5, LIGHT_BG)
    add_header(s5, "Technology Stack", 5)

    add_card(s5, 0.7, 1.5, 3.7, 2.4, "Frontend", [
        "HTML5 & CSS3",
        "Bootstrap 5 (Responsive UI)",
        "Bootstrap Icons",
        "Basic JavaScript validation",
    ], INDIGO)
    add_card(s5, 4.8, 1.5, 3.7, 2.4, "Backend", [
        "Core PHP 8+",
        "PHP Sessions for login state",
        "password_hash() & verify()",
        "CSRF & XSS protection",
    ], TEAL)
    add_card(s5, 8.9, 1.5, 3.7, 2.4, "Database & Server", [
        "MySQL / MariaDB",
        "PHP PDO Prepared Statements",
        "XAMPP Local Server",
        "phpMyAdmin for DB admin",
    ], GREEN)

    note = s5.shapes.add_textbox(Inches(0.7), Inches(4.35), Inches(11.9), Inches(1.5))
    np = note.text_frame.paragraphs[0]
    np.text = "Why Core PHP + Bootstrap 5? Lightweight, zero build tools required, runs directly in XAMPP, and easy to understand and explain in a viva."
    np.font.name = "Calibri"
    np.font.size = Pt(18)
    np.font.italic = True
    np.font.color.rgb = SLATE

    # ------------------------------------------------------------------
    # Slide 6: Main Features
    # ------------------------------------------------------------------
    s6 = prs.slides.add_slide(blank_layout)
    set_slide_background(s6, LIGHT_BG)
    add_header(s6, "Main Features & Modules", 6)

    add_card(s6, 0.7, 1.45, 5.75, 2.5, "1. Authentication Module", [
        "New user registration & validation",
        "Secure login with hashed passwords",
        "Session-based access control & logout",
    ], INDIGO)
    add_card(s6, 6.85, 1.45, 5.75, 2.5, "2. Dashboard Module", [
        "Total Income, Expenses & Remaining Balance",
        "Current month summary & category bars",
        "Recent 6 transactions overview",
    ], TEAL)
    add_card(s6, 0.7, 4.2, 5.75, 2.5, "3. Transactions Module", [
        "Add Income or Expense with description",
        "Filter by Type & Category",
        "Edit & Delete with confirmation",
    ], GREEN)
    add_card(s6, 6.85, 4.2, 5.75, 2.5, "4. Monthly Reports Module", [
        "Filter records by Month and Year",
        "Monthly Income vs Expense totals",
        "Clean tabular breakdown",
    ], NAVY)

    # ------------------------------------------------------------------
    # Slide 7: Working Process
    # ------------------------------------------------------------------
    s7 = prs.slides.add_slide(blank_layout)
    set_slide_background(s7, LIGHT_BG)
    add_header(s7, "Working Process (Step-by-Step Flow)", 7)
    add_bullets(s7, [
        ("Step 1 – Register / Login", "User creates an account or signs in; PHP stores user_id in $_SESSION."),
        ("Step 2 – View Dashboard", "Dashboard queries MySQL SUM() totals for the logged-in user and displays KPIs."),
        ("Step 3 – Add Transaction", "User chooses Income or Expense, enters Title, Amount, Category, Date, and Notes."),
        ("Step 4 – Manage Records", "User can filter transactions, edit mistakes, or delete records with confirmation."),
        ("Step 5 – Check Reports", "User selects any Month and Year on reports.php to review monthly financial totals."),
    ])

    # ------------------------------------------------------------------
    # Slide 8: Database Design
    # ------------------------------------------------------------------
    s8 = prs.slides.add_slide(blank_layout)
    set_slide_background(s8, LIGHT_BG)
    add_header(s8, "Database Design (3 Normalized Tables)", 8)

    add_card(s8, 0.7, 1.5, 3.7, 3.6, "1. users", [
        "id (Primary Key)",
        "name (VARCHAR 100)",
        "email (UNIQUE)",
        "password (Hashed)",
        "created_at (TIMESTAMP)",
    ], INDIGO)
    add_card(s8, 4.8, 1.5, 3.7, 3.6, "2. categories", [
        "id (Primary Key)",
        "name (UNIQUE)",
        "type (income/expense/both)",
        "7 Defaults: Food, Travel,",
        "Shopping, Education, Bills, Salary, Others",
    ], TEAL)
    add_card(s8, 8.9, 1.5, 3.7, 3.6, "3. transactions", [
        "id (Primary Key)",
        "user_id (FK -> users.id)",
        "category_id (FK -> categories.id)",
        "title, amount, type",
        "transaction_date, description",
    ], GREEN)

    rel = s8.shapes.add_textbox(Inches(0.7), Inches(5.45), Inches(11.9), Inches(0.8))
    rp = rel.text_frame.paragraphs[0]
    rp.text = "Relationships: One User has Many Transactions (1:N)  |  One Category has Many Transactions (1:N)"
    rp.font.name = "Calibri"
    rp.font.size = Pt(18)
    rp.font.bold = True
    rp.font.color.rgb = NAVY

    # ------------------------------------------------------------------
    # Slide 9: System Architecture
    # ------------------------------------------------------------------
    s9 = prs.slides.add_slide(blank_layout)
    set_slide_background(s9, LIGHT_BG)
    add_header(s9, "System Architecture (3-Tier Web Model)", 9)

    add_card(s9, 0.7, 1.8, 3.5, 3.1, "Presentation Layer", [
        "HTML5 & Bootstrap 5 UI",
        "Sidebar & Summary Cards",
        "Forms & Basic JS Alerts",
    ], INDIGO)
    add_card(s9, 4.9, 1.8, 3.5, 3.1, "Application Layer", [
        "Apache + Core PHP",
        "Session Authentication",
        "Input Validation & CRUD",
    ], TEAL)
    add_card(s9, 9.1, 1.8, 3.5, 3.1, "Data Layer", [
        "MySQL Database",
        "PHP PDO Driver",
        "Prepared Statements",
    ], GREEN)

    # Arrows between tiers
    for x in (4.3, 8.5):
        arr = s9.shapes.add_textbox(Inches(x), Inches(3.0), Inches(0.5), Inches(0.6))
        ap = arr.text_frame.paragraphs[0]
        ap.text = "⇄"
        ap.alignment = PP_ALIGN.CENTER
        ap.font.size = Pt(28)
        ap.font.bold = True
        ap.font.color.rgb = NAVY

    # ------------------------------------------------------------------
    # Slide 10: Demonstration and Testing
    # ------------------------------------------------------------------
    s10 = prs.slides.add_slide(blank_layout)
    set_slide_background(s10, LIGHT_BG)
    add_header(s10, "Demonstration & Testing", 10)

    add_bullets(s10, [
        ("Auth & Security Tested", "Unauthorized dashboard access redirects to login; passwords hashed with bcrypt."),
        ("CRUD Verified", "Adding, editing, and deleting transactions immediately updates Dashboard totals."),
        ("Monthly Filter Verified", "Reports page accurately calculates income and expenses per selected month."),
    ], left=0.7, top=1.4, width=11.9, height=2.2, font_size=18)

    # Screenshot Placeholder Cards
    add_card(s10, 0.7, 3.75, 5.75, 2.8, "[Insert Screenshot 1: Dashboard]", [
        "Run http://localhost/budgify in XAMPP",
        "Take screenshot of Dashboard KPIs & Recent Table",
        "Replace this placeholder box in PowerPoint",
    ], INDIGO)
    add_card(s10, 6.85, 3.75, 5.75, 2.8, "[Insert Screenshot 2: Transactions / Reports]", [
        "Take screenshot of Add/View Transactions or Reports",
        "Paste image here before your college presentation",
    ], TEAL)

    # ------------------------------------------------------------------
    # Slide 11: Conclusion and Future Scope
    # ------------------------------------------------------------------
    s11 = prs.slides.add_slide(blank_layout)
    set_slide_background(s11, LIGHT_BG)
    add_header(s11, "Conclusion & Future Scope", 11)

    add_card(s11, 0.7, 1.55, 5.75, 4.6, "Conclusion", [
        "Budgify delivers a clean, reliable personal expense tracker.",
        "Built with beginner-friendly Core PHP, PDO, and Bootstrap 5.",
        "Uses a clean 3-table relational MySQL schema.",
        "Protects user privacy with sessions, hashing, and prepared statements.",
    ], INDIGO)

    add_card(s11, 6.85, 1.55, 5.75, 4.6, "Future Scope", [
        "Export monthly reports to PDF or CSV spreadsheets.",
        "Add monthly budget limits with overspending alerts.",
        "Add interactive visual charts for yearly trends.",
        "Support recurring bills and savings goals.",
    ], GREEN)

    output_file = "Budgify_Presentation.pptx"
    prs.save(output_file)
    print(f"Presentation saved successfully: {output_file}")


if __name__ == "__main__":
    build_presentation()

