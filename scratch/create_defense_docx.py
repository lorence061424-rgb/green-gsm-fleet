import os
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._element.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._element.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def add_callout(doc, text, title="HIRNA SCENARIO EXAMPLE", border_color="CE2029", bg_color="F8FAFC"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)
    
    tcPr = cell._element.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>'
        f'<w:top w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'<w:bottom w:val="none"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(4)
    run_t = p.add_run(f"📌 {title}\n")
    run_t.bold = True
    run_t.font.name = "Calibri"
    run_t.font.size = Pt(11)
    run_t.font.color.rgb = RGBColor(206, 32, 41)
    
    run_b = p.add_run(text)
    run_b.font.name = "Calibri"
    run_b.font.size = Pt(10.5)
    run_b.font.color.rgb = RGBColor(30, 41, 59)
    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def build_defense_script():
    doc = Document()
    
    # Page margins
    for section in doc.sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)
        
    # Styles Setup
    normal_style = doc.styles['Normal']
    normal_style.font.name = 'Calibri'
    normal_style.font.size = Pt(11)
    normal_style.font.color.rgb = RGBColor(30, 41, 59) # Slate Dark
    
    # Title Header Block
    header_tbl = doc.add_table(rows=1, cols=1)
    header_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    h_cell = header_tbl.cell(0, 0)
    set_cell_background(h_cell, "CE2029") # Hirna Red
    set_cell_margins(h_cell, top=200, bottom=200, left=200, right=200)
    
    hp = h_cell.paragraphs[0]
    hp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hrun1 = hp.add_run("HIRNA FLEET & VEHICLE MANAGEMENT SYSTEM\n")
    hrun1.bold = True
    hrun1.font.size = Pt(18)
    hrun1.font.color.rgb = RGBColor(255, 255, 255)
    
    hrun2 = hp.add_run("PRE-ORAL DEFENSE SCRIPT & MODULE PRESENTATION GUIDE\n")
    hrun2.bold = True
    hrun2.font.size = Pt(14)
    hrun2.font.color.rgb = RGBColor(254, 242, 242)
    
    hrun3 = hp.add_run("Team 7: FVM | VRDS | DTPM | TCAO — Complete Taglish Defense Script with Real Hirna Scenarios")
    hrun3.font.size = Pt(10.5)
    hrun3.font.italic = True
    hrun3.font.color.rgb = RGBColor(255, 226, 226)
    
    p_spacer = doc.add_paragraph()
    p_spacer.paragraph_format.space_after = Pt(12)
    
    # Table of Contents / Summary Metadata
    meta_tbl = doc.add_table(rows=4, cols=2)
    meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_data = [
        ("Project Scope:", "Team 7 - Fleet & Vehicle Management, Dispatch, Performance & Cost Optimization"),
        ("Target Audience:", "Pre-Oral Capstone Defense Panelists & Academic Evaluators"),
        ("Presentation Format:", "Taglish Dialogue & Live System Demonstration Script"),
        ("Key Integration Points:", "Multi-Team Pipeline (Team 2 Booking, Team 3 HR, Team 4 Finance, Team 5 Payroll, Team 6 Supply Chain, Team 8 Maintenance, Team 9 Driver Registry, Team 10 Ratings)")
    ]
    for idx, (label, val) in enumerate(meta_data):
        row = meta_tbl.rows[idx]
        cell_lbl, cell_val = row.cells[0], row.cells[1]
        set_cell_background(cell_lbl, "F1F5F9")
        set_cell_background(cell_val, "FFFFFF")
        set_cell_margins(cell_lbl, top=80, bottom=80, left=100, right=100)
        set_cell_margins(cell_val, top=80, bottom=80, left=100, right=100)
        
        p0 = cell_lbl.paragraphs[0]
        r0 = p0.add_run(label)
        r0.bold = True
        r0.font.size = Pt(10)
        r0.font.color.rgb = RGBColor(206, 32, 41)
        
        p1 = cell_val.paragraphs[0]
        r1 = p1.add_run(val)
        r1.font.size = Pt(10)
        r1.font.color.rgb = RGBColor(30, 41, 59)
        
    doc.add_paragraph().paragraph_format.space_after = Pt(12)
    
    # ---------------------------------------------------------
    # PART 1: SYSTEM OVERVIEW & INTRODUCTION
    # ---------------------------------------------------------
    h1 = doc.add_heading("PART 1: SYSTEM OVERVIEW & INTRODUCTORY SCRIPT", level=1)
    h1.runs[0].font.color.rgb = RGBColor(206, 32, 41)
    
    intro_p = doc.add_paragraph()
    intro_p.add_run("🎙️ SPEAKER CUE: Presenter stands confident, welcomes the panel, and presents the core vision of the Hirna Fleet & Vehicle Management System.").italic = True
    
    script_intro = (
        "\"Good morning / good afternoon to our respected panelists, advisors, and guests. "
        "We are Team 7, and today we present the core operational engine of Hirna: the Fleet & Vehicle Management System.\n\n"
        "Ang Hirna po ay hindi lamang isang simpleng ride-hailing app; ito ay isang fully integrated transport ecosystem "
        "na nag-uugnay sa ating electric vehicles (EVs), traditional shuttles, professional drivers, and corporate clients across Metro Manila. "
        "Bago dumating ang aming system, ang pamamahala ng fleet, vehicle reservation, driver scoring, at transport cost tracking ay ginagawa nang mano-mano sa magkakahiwalay na Excel sheets. "
        "Dahil dito, nagkakaroon ng double-booking ng sasakyan, kakulangan sa maintenance tracking, hindi patas na driver penalty, at kawalan ng malinaw na financial visibility sa totoong gastos bawat kilometro (₱/km).\n\n"
        "Upang solusyunan ito, binuo ng Team 7 ang apat na magkakaugnay na modules:\n"
        "1. Fleet & Vehicle Management (FVM) — Ang digital garage at inventory management system.\n"
        "2. Vehicle Reservation & Dispatch System (VRDS) — Ang smart schedule calendar at automated dispatch engine.\n"
        "3. Driver & Trip Performance Monitoring (DTPM) — Ang live GPS telematics map at 24-hour HR dispute verified scorecard.\n"
        "4. Transport Cost Analysis & Optimization (TCAO) — Ang financial intelligence tool para sa management.\n\n"
        "Ngayon po, papasok tayo sa detalyadong pag-unawa at actual business scenarios ng bawat module.\""
    )
    doc.add_paragraph(script_intro).paragraph_format.space_after = Pt(12)
    
    # ---------------------------------------------------------
    # PART 2: MODULE-BY-MODULE EXPLANATION & SCENARIOS
    # ---------------------------------------------------------
    h2 = doc.add_heading("PART 2: MODULE-BY-MODULE PROCESS & HIRNA COMPANY SCENARIOS", level=1)
    h2.runs[0].font.color.rgb = RGBColor(206, 32, 41)
    
    # --- MODULE 1: FVM ---
    h3_1 = doc.add_heading("MODULE 1: Fleet & Vehicle Management (FVM)", level=2)
    h3_1.runs[0].font.color.rgb = RGBColor(30, 41, 59)
    
    fvm_desc = (
        "📌 Module Process & Technical Purpose:\n"
        "Ang Fleet & Vehicle Management (FVM) ang nagsisilbing central database ng lahat ng sasakyan sa Hirna. "
        "Naka-categorize dito ang ating mga sasakyan katulad ng Electric Vehicles (e.g., VinFast VF8, BYD E6), Gasoline Vehicles (Sedans/SUVs), at Modern Traysikels. "
        "Binabantayan ng FVM ang real-time Status (Available, In Trip, Maintenance, Retired), Odometer Reading (km), Fuel/Battery Telematics (SoC %), at OR/CR Registration Expiry.\n\n"
        "Bukod dito, nakakabit din dito ang Read-Only Driver Directory Sync na nanggagaling mula sa Team 9 (Driver Registry), "
        "at ang direct linkage sa Team 6 (Supply Chain & Spare Parts Inventory) para sa pag-request ng ekstrang gulong, langis, at baterya bago dalhin sa Team 8 (Motorshop & Maintenance)."
    )
    doc.add_paragraph(fvm_desc).paragraph_format.space_after = Pt(8)
    
    fvm_scenario = (
        "Isang umaga sa Hirna Central Depot sa Pasig, pumasok sa system ang bagong electric vehicle na VinFast VF8 (Plate No: EV-2026-88). "
        "Ginamit ng Fleet Officer ang FVM module para i-register ang profile ng EV, kasama ang battery capacity (82 kWh) at initial odometer reading (1,200 km).\n\n"
        "Habang tumatakbo ang linggo, napansin ng system na umabot na sa 5,000 km ang mileage nito at bumaba sa 15% ang Battery Health Alert. "
        "Imbes na maghintay na mamatayan ng baterya sa kalsada, ang FVM ay awtomatikong nag-trigger ng Maintenance Alert. "
        "Agad na pumasok ang Fleet Officer sa FVM tab, pumili ng 'Request Spare Battery Module' na dadaan sa Team 6 (Supply Chain), "
        "bago i-schedule ang preventive maintenance slot sa Team 8 (Motorshop). Dahil dito, nananatiling safe at reliable ang sasakyan bago pa ito i-dispatch sa mga pasahero."
    )
    add_callout(doc, fvm_scenario, title="HIRNA COMPANY SCENARIO 1: FVM Fleet Onboarding & Preventive Maintenance Trigger")
    
    # --- MODULE 2: VRDS ---
    h3_2 = doc.add_heading("MODULE 2: Vehicle Reservation & Dispatch System (VRDS)", level=2)
    h3_2.runs[0].font.color.rgb = RGBColor(30, 41, 59)
    
    vrds_desc = (
        "📌 Module Process & Technical Purpose:\n"
        "Ang Vehicle Reservation & Dispatch System (VRDS) ay ang smart booking scheduler ng Hirna. "
        "Pinapayagan nito ang mga corporate clients, VIP department heads, o internal operation managers na mag-book ng sasakyan para sa partikular na petsa at oras. "
        "Mayroon itong Visual Schedule Calendar na nagpapakita ng active timelines at 2-Column Responsive Dispatch Console.\n\n"
        "Key Feature — Auto-Conflict Validation Engine: Kapag may gustong mag-reserve ng sasakyan o mag-assign ng driver sa oras na may umiiral nang biyahe, "
        "awtomatikong haharangin ng system ang request at mag-o-overlap warning upang maiwasan ang double-booking. "
        "Ang matagumpay na dispatch ay kumokonekta rin sa Team 2 (Booking Engine) at Team 8 (Vehicle Readiness)."
    )
    doc.add_paragraph(vrds_desc).paragraph_format.space_after = Pt(8)
    
    vrds_scenario = (
        "Mayroong VIP Executive Flight Pick-up sa NAIA Terminal 3 para sa Hirna Corporate Board sa ganap na 2:00 PM hanggang 5:00 PM. "
        "Ang Executive Assistant ay nag-submit ng Reservation Request gamit ang VRDS portal para sa Platinum SUV (Plate: VIP-777).\n\n"
        "Subalit, sa kaparehong oras (3:00 PM), may isa pang junior staff na nagtangkang mag-reserve ng parehong VIP SUV. "
        "Awtomatikong pumasok ang Auto-Conflict Validation Engine ng VRDS at nag-prompt ng: 'Conflict Detected: Vehicle VIP-777 is already assigned to VIP Airport Transfer from 14:00 to 17:00.' "
        "Nag-suggest ang system ng available alternate vehicle (VinFast EV-102). "
        "Inaprubahan ng Dispatcher ang airport pickup request, awtomatikong in-assign si Driver Juan Dela Cruz (na available sa Team 9 Driver Directory), at nag-generate ng Dispatch Ticket sa system."
    )
    add_callout(doc, vrds_scenario, title="HIRNA COMPANY SCENARIO 2: VRDS Executive Reservation & Conflict Engine")
    
    # --- MODULE 3: DTPM ---
    h3_3 = doc.add_heading("MODULE 3: Driver & Trip Performance Monitoring (DTPM)", level=2)
    h3_3.runs[0].font.color.rgb = RGBColor(30, 41, 59)
    
    dtpm_desc = (
        "📌 Module Process & Technical Purpose:\n"
        "Ang Driver & Trip Performance Monitoring (DTPM) ang puso ng safety at operational analytics. "
        "Naglalaman ito ng Live Metro Manila GPS Simulation Map na nagtatala ng totoong biyahe ng mga sasakyan sa EDSA, C5, at Roxas Boulevard, "
        "gamit ang Haversine Formula para sa eksaktong kilometro at EV Energy Consumption Prediction Model.\n\n"
        "Key Feature — 24-Hour HR Dispute Verification Grace Period: Kapag nakatanggap ang driver ng 1-Star Rating mula sa pasahero (Team 10), "
        "INDI agad kaltas sa sweldo! Papatak muna ito sa 24-Hour Pending Dispute Status sa DTPM. "
        "Bibigyan ng pagkakataon ang Team 3 (HR) at driver na magsumite ng dashcam footage o paliwanag. "
        "Kapag na-verify na valid customer complaint (e.g. overspeeding), saka pa lamang papasok ang -7.0% Safety Score reduction at ₱300 Payroll Penalty Log sa Team 5 (Payroll)."
    )
    doc.add_paragraph(dtpm_desc).paragraph_format.space_after = Pt(8)
    
    dtpm_scenario = (
        "Habang bumabiyahe si Driver Ricardo Dalisay sa EDSA Cubao sector, na-detect ng DTPM Telematics Map na lumampas siya sa speed limit (85 km/h) at nagkaroon ng Harsh Braking Event. "
        "Pagkababa ng pasahero sa Makati, nagbigay ito ng 1-Star Rating sa Team 10 Rating System dahil sa takot sa mabilis na pagmamaneho.\n\n"
        "Sa lumang sistema, kaltas-sweldo agad. Pero sa Hirna DTPM, pumasok ang rating sa '24-Hour HR Dispute Verification Grace Period'. "
        "Nakatanggap si Team 3 (HR) ng notification. Nirepaso ng HR Officer ang GPS telemetry logs ng DTPM at nakitang totoong nag-overspeed si Ricardo sa Cubao. "
        "Pagkalipas ng 24 oras na HR review, in-approve ng HR ang violation. "
        "Dito na pumasok ang automatic scorecard update: bumaba ng -7.0% ang Safety Score ni Ricardo at nag-generate ang system ng ₱300 Payroll Deduction Log para sa Team 5 (Payroll)."
    )
    add_callout(doc, dtpm_scenario, title="HIRNA COMPANY SCENARIO 3: DTPM Live Tracking & 24h HR Dispute Verification")
    
    # --- MODULE 4: TCAO ---
    h3_4 = doc.add_heading("MODULE 4: Transport Cost Analysis & Optimization (TCAO)", level=2)
    h3_4.runs[0].font.color.rgb = RGBColor(30, 41, 59)
    
    tcao_desc = (
        "📌 Module Process & Technical Purpose:\n"
        "Ang Transport Cost Analysis & Optimization (TCAO) ay ang executive financial dashboard ng system. "
        "Dinidisenyo ito upang kalkulahin ang Total Cost per Kilometer (₱/km), binubuo ng Fuel/Charging Costs, Fleet Maintenance Repairs, Driver Payroll Allocations, at Toll/Other Operational Expenses.\n\n"
        "Key Feature — Management Date Range Filter & Financial Exports: Pinapayagan ng TCAO ang management na mag-filter ng dates (e.g. Sept 1 - Sept 25, 2026, Year-to-Date, or Custom Presets) "
        "upang makita ang cost breakdown. Nag-ge-generate din ito ng downloadable CSV at PDF reports para sa Team 4 (Finance & General Ledger) at Team 5 (Payroll Reconciliation)."
    )
    doc.add_paragraph(tcao_desc).paragraph_format.space_after = Pt(8)
    
    tcao_scenario = (
        "Gusto ng Hirna Chief Financial Officer (CFO) na malaman kung magkano ang natipid ng kumpanya sa paglipat mula sa Diesel Vans patungong Electric Vehicles (EVs) para sa buong buwan ng Setyembre 2026.\n\n"
        "Binuksan ng CFO ang TCAO module, ginamit ang Management Date Range Filter at pinili ang 'September 1, 2026 to September 25, 2026'. "
        "Agad na in-upate ng TCAO ang visual charts: Naitala na ang average cost per kilometer ng Diesel fleet ay ₱14.50 / km, samantalang ang EV fleet ay ₱4.20 / km lamang! "
        "Nagpakita rin ang AI Cost Optimizer na natipid ng Hirna ang kabuuang ₱185,000 sa fuel expenses ngayong buwan. "
        "I-n-export ng CFO ang TCAO Executive Summary sa PDF/CSV format at agad na pinalabas sa Board Meeting bilang patunay ng ROI ng kumpanya."
    )
    add_callout(doc, tcao_scenario, title="HIRNA COMPANY SCENARIO 4: TCAO Financial Date Range Filter & AI Cost Savings")
    
    # ---------------------------------------------------------
    # PART 3: INTER-TEAM PIPELINE INTEGRATION
    # ---------------------------------------------------------
    h3 = doc.add_heading("PART 3: MULTI-TEAM INTER-SYSTEM PIPELINE (TEAMS 1 - 10)", level=1)
    h3.runs[0].font.color.rgb = RGBColor(206, 32, 41)
    
    pipe_intro = doc.add_paragraph()
    pipe_intro.add_run("Ang aming Team 7 (FVM, VRDS, DTPM, TCAO) ay ang sentro ng operasyon. Narito ang buong daloy ng datos sa iba't ibang teams:").font.size = Pt(11)
    
    pipe_tbl = doc.add_table(rows=9, cols=3)
    pipe_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    headers = ["Connected Team", "Pipeline Data Flow", "Team 7 Module Linkage"]
    hdr_row = pipe_tbl.rows[0]
    for idx, text in enumerate(headers):
        cell = hdr_row.cells[idx]
        set_cell_background(cell, "CE2029")
        set_cell_margins(cell, top=100, bottom=100, left=120, right=120)
        p = cell.paragraphs[0]
        r = p.add_run(text)
        r.bold = True
        r.font.size = Pt(10)
        r.font.color.rgb = RGBColor(255, 255, 255)
        
    pipelines = [
        ("Team 2 (Booking Engine)", "Tumatanggap ng verified reservation at dispatch requests para sa trip execution.", "VRDS (Dispatch System)"),
        ("Team 3 (HR & Staff)", "Nagtatanggap ng 1-Star Dispute Alerts para sa 24h grace period verification & safety logs.", "DTPM (Performance Scoring)"),
        ("Team 4 (Finance)", "Kumukuha ng TCAO financial report feeds (₱/km, total operational cost) para sa general ledger.", "TCAO (Cost Analysis)"),
        ("Team 5 (Payroll)", "Pinapadalhan ng verified driver penalties (e.g. ₱300 fine) at trip incentive bonuses.", "DTPM & TCAO"),
        ("Team 6 (Supply Chain)", "Tumatanggap ng parts request (tires, batteries, oil) mula sa fleet officers bago dalhin sa shop.", "FVM (Inventory & Parts Request)"),
        ("Team 8 (Maintenance/Shop)", "Tumatanggap ng maintenance work orders para sa PM/Corrective repairs ng mga sasakyan.", "FVM & VRDS"),
        ("Team 9 (Driver Registry)", "Nagse-sync ng driver master list, license validity, at active driver assignments.", "FVM Directory & VRDS"),
        ("Team 10 (Customer Ratings)", "Nagpapadala ng customer passenger ratings (1 to 5 Stars) at feedback reviews.", "DTPM (Safety Scorecard)")
    ]
    
    for row_idx, (t_name, t_flow, t_mod) in enumerate(pipelines, start=1):
        row = pipe_tbl.rows[row_idx]
        c0, c1, c2 = row.cells[0], row.cells[1], row.cells[2]
        bg = "F8FAFC" if row_idx % 2 == 1 else "FFFFFF"
        set_cell_background(c0, bg)
        set_cell_background(c1, bg)
        set_cell_background(c2, bg)
        for c in (c0, c1, c2):
            set_cell_margins(c, top=80, bottom=80, left=100, right=100)
            
        p0 = c0.paragraphs[0]
        r0 = p0.add_run(t_name)
        r0.bold = True
        r0.font.size = Pt(9.5)
        r0.font.color.rgb = RGBColor(206, 32, 41)
        
        p1 = c1.paragraphs[0]
        r1 = p1.add_run(t_flow)
        r1.font.size = Pt(9.5)
        
        p2 = c2.paragraphs[0]
        r2 = p2.add_run(t_mod)
        r2.bold = True
        r2.font.size = Pt(9.5)
        r2.font.color.rgb = RGBColor(30, 41, 59)
        
    doc.add_paragraph().paragraph_format.space_after = Pt(12)
    
    # ---------------------------------------------------------
    # PART 4: PANELIST DEFENSE Q&A CHEATSHEET
    # ---------------------------------------------------------
    h4 = doc.add_heading("PART 4: PANELIST DEFENSE Q&A CHEATSHEET", level=1)
    h4.runs[0].font.color.rgb = RGBColor(206, 32, 41)
    
    qa_list = [
        ("Q1: Paano ninyo pinipigilan ang hindi makatarungang pagkakaltas sa sweldo ng driver kapag nagbigay ng 1-star rating ang galit o biased na pasahero?",
         "SAGOT: Sa aming DTPM module, ipinatupad namin ang '24-Hour HR Dispute Verification Grace Period'. Kapag may 1-star rating, HINDI ito awtomatikong binabawas sa payroll. "
         "Papatak ito bilang 'Pending HR Review'. Bibigyan ng 24 oras ang Team 3 (HR) upang suriin ang GPS telematics, speed logs, at dashcam proof. Kapag napatunayang mali ang pasahero o galit lang, ie-evict ng HR ang penalty at walang kaltas na magaganap sa Team 5 Payroll."),
        
        ("Q2: Ano ang nangyayari sa PMS (Preventive Maintenance)? Kumuha ba agad ng parts ang driver o dadaan sa Supply Chain?",
         "SAGOT: Hindi pwedeng direktang bumili o kumuha ng parts ang driver. Sa aming workflow: Kapag nakakita ng anomaly o naabot ang mileage limit sa FVM, ang Fleet Officer o Maintenance Head ay magsa-submit ng Parts Request sa Team 6 (Supply Chain). Kapag inilabas na ng Team 6 ang pyesa, saka ito dadalhin sa Team 8 (Motorshop) kasabay ng sasakyan para sa pagpapalit."),
        
        ("Q3: Paano sinusukat ng TCAO ang Transport Cost per Kilometer (₱/km)?",
         "SAGOT: Ginagamit ng TCAO ang formula: Total Operational Cost / Total Distance Traveled (km). Ang Total Operational Cost ay summation ng Fuel/EV Charging Expenses, Maintenance Repair Orders (mula Team 8), at Driver Allowance. May kasama ring Management Date Range Filter ito upang ma-analyze ang gastos sa partikular na linggo o buwan."),
        
        ("Q4: Paano sinisigurado ng VRDS na walang magaganap na double-booking sa mga sasakyan at driver?",
         "SAGOT: Ang VRDS ay may Real-Time Auto-Conflict Validation Engine. Bago i-save ang reservation o dispatch, tinitingnan ng system sa database kung ang specific Vehicle ID o Driver ID ay may nag-e-exist nang Active/Scheduled Trip sa piniling Start Time at End Time window. Kapag may overlap, magse-serve ito ng 400 Validation Error at ie-suggest ang mga available na alternatibong sasakyan.")
    ]
    
    for q_title, q_ans in qa_list:
        p_q = doc.add_paragraph()
        p_q.paragraph_format.space_before = Pt(6)
        p_q.paragraph_format.space_after = Pt(2)
        r_q = p_q.add_run(f"❓ {q_title}")
        r_q.bold = True
        r_q.font.size = Pt(11)
        r_q.font.color.rgb = RGBColor(206, 32, 41)
        
        p_a = doc.add_paragraph()
        p_a.paragraph_format.space_after = Pt(8)
        r_a = p_a.add_run(q_ans)
        r_a.font.size = Pt(10.5)
        r_a.font.color.rgb = RGBColor(30, 41, 59)
        
    # Conclusion Line
    p_end = doc.add_paragraph()
    p_end.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_end.paragraph_format.space_before = Pt(16)
    r_end = p_end.add_run("--- END OF DEFENSE PRESENTATION SCRIPT ---")
    r_end.bold = True
    r_end.font.size = Pt(11)
    r_end.font.color.rgb = RGBColor(100, 116, 139)
    
    return doc

if __name__ == "__main__":
    doc = build_defense_script()
    
    # Save in workspace
    ws_path = r"c:\xamppp\htdocs\TNVS\Hirna_Pre_Oral_Defense_Script_Taglish.docx"
    doc.save(ws_path)
    print(f"Saved workspace file: {ws_path}")
    
    # Save in artifact dir
    art_dir = r"C:\Users\Lorence\.gemini\antigravity\brain\3bc4e34d-8866-4b43-87d1-94220eea0331"
    os.makedirs(art_dir, exist_ok=True)
    art_path = os.path.join(art_dir, "Hirna_Pre_Oral_Defense_Script_Taglish.docx")
    doc.save(art_path)
    print(f"Saved artifact file: {art_path}")
