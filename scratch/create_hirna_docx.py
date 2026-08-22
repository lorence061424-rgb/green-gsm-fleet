import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
import os

doc = docx.Document()

# Set standard margins (1 inch)
for section in doc.sections:
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)

# Color Palette: Hirna Crimson Red (#CE2029), Dark Crimson (#7F1D1D), Sun Gold (#F59E0B)
PRIMARY_COLOR = RGBColor(206, 32, 41)
SECONDARY_COLOR = RGBColor(127, 29, 29)
GOLD_COLOR = RGBColor(245, 158, 11)
DARK_TEXT = RGBColor(40, 40, 40)

def set_cell_background(cell, fill_hex):
    tcPr = cell._element.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

# Title Block
title_p = doc.add_paragraph()
title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_sub = title_p.add_run("HIRNA MOBILITY SOLUTIONS INC.\n")
run_sub.font.name = 'Arial'
run_sub.font.size = Pt(12)
run_sub.font.bold = True
run_sub.font.color.rgb = GOLD_COLOR

run_title = title_p.add_run("COMPLETE ENTERPRISE SYSTEM ARCHITECTURE & MODULE INTEGRATION PROCESS\n(TAGLISH VERSION)")
run_title.font.name = 'Arial'
run_title.font.size = Pt(18)
run_title.font.bold = True
run_title.font.color.rgb = PRIMARY_COLOR

subtitle_p = doc.add_paragraph()
subtitle_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_sub2 = subtitle_p.add_run("Team 7: Fleet and Transportation Management System (Complete Integration with Teams 1 to 10 & HR 1–4)\n")
run_sub2.font.name = 'Arial'
run_sub2.font.size = Pt(10)
run_sub2.font.italic = True
run_sub2.font.color.rgb = SECONDARY_COLOR

doc.add_paragraph().paragraph_format.space_after = Pt(12)

def add_heading_1(text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(16)
    h.paragraph_format.space_after = Pt(6)
    run = h.add_run(text)
    run.font.name = 'Arial'
    run.font.size = Pt(13)
    run.font.bold = True
    run.font.color.rgb = PRIMARY_COLOR
    return h

def add_body_paragraph(text):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    run.font.name = 'Arial'
    run.font.size = Pt(10.5)
    run.font.color.rgb = DARK_TEXT
    return p

# 1. EXECUTIVE SUMMARY (TAGLISH)
add_heading_1("1. PANGKALAHATANG ALOY AT PROSESO NG SISTEMA (OVERVIEW)")
add_body_paragraph(
    "Ang Fleet and Transportation Management System (Team 7) para sa Hirna Mobility Solutions Inc. ay ang sentral na nag-a-automate "
    "ng buong fleet operations. Kumokonekta ito sa lahat ng kasamang enterprise teams—mula sa HR Management Systems (Teams 1 hanggang 4), "
    "Financial Accounting (Team 5), Supply Chain & Inventory (Team 6), Facilities & Hubs (Team 8), Operations (Team 9), at Passenger CRM Booking (Team 10)."
)

add_body_paragraph(
    "Kina-calculate atina-track ng Team 7 ang buong biyahe ng sasakyan—mula sa pag-verify ng bagong hire na driver mula sa HR, "
    "pagre-reserve ng sasakyan sa dispatch, live Leaflet GPS tracking, AI prediction ng gasolina/diesel/EV battery, hanggang sa "
    "preventive maintenance (PMS) at real-time Transport Cost Analysis (TCAO)."
)

# 2. INTERNAL MODULE CONNECTIONS WITHIN TEAM 7
add_heading_1("2. PAANO NAG-UUSAP ANG MGA MODULE SA LOOB NG TEAM 7")
add_body_paragraph(
    "Mayroong 7 main modules ang Team 7 na tuloy-tuloy na nagpapalitan ng datos para maging transparent at accurate ang daily operations:"
)

table_modules = doc.add_table(rows=1, cols=3)
table_modules.alignment = WD_TABLE_ALIGNMENT.CENTER
hdr_cells = table_modules.rows[0].cells
headers = ["Module Name", "Main na Gawain", "Paano Kumokonekta sa Ibang Module"]
for i, head in enumerate(headers):
    hdr_cells[i].text = head
    set_cell_background(hdr_cells[i], "CE2029")
    p = hdr_cells[i].paragraphs[0]
    p.runs[0].font.color.rgb = RGBColor(255, 255, 255)
    p.runs[0].font.bold = True

modules_data = [
    ("1. Fleet & Vehicle Management (FVM)", "Central listahan ng lahat ng sasakyan (Hirna Taxis, Vans, MPVs, Traysikel) at driver profiles.", "Ipinapasa ang status ng sasakyan (Active, Maintenance, Offline) sa Dispatch module. Hindi pwedeng i-dispatch kapag naka-Maintenance."),
    ("2. Vehicle Reservation & Dispatch (VRD)", "Pinapares ang available na sasakyan sa tamang driver at gumagawa ng unique Booking Reference ID.", "Ipinapasa ang approved dispatch details sa Route Planning at Live GPS Telemetry map."),
    ("3. Route Planning & Optimization (RPO)", "Kina-calculate ang 3 pinakamagandang ruta (Eco, Highway, City Bypass) para sa Gas, Diesel, at EV.", "Ipinapasa ang estimated kilometro at trapik sa AI Fuel Predictor engine bago bumiyahe."),
    ("4. Driver & Trip Performance (DTPM)", "Naka-live GPS tracking sa bilis (km/h), idling time, at driver eco-safety score (100% baseline).", "Nagse-send ng alert sa Maintenance (PMS) kapag paulit-ulit ang speeding o harsh braking ng driver."),
    ("5. Fuel Management System (FMS)", "Nagtatala ng gas/diesel refill at EV charging receipts kasama ang presyo (₱/L o ₱/kWh).", "Ipinapasa ang kabuuang gastos sa gasolina at kuryente diretso sa Transport Cost Analysis (TCAO)."),
    ("6. Preventive Maintenance (PMS)", "Nag-aayos ng maintenance schedule (oil change, tire alignment) at repair expenses.", "Awtomatikong ginagawang 'Maintenance' ang status ng sasakyan habang inaayos, at 'Active' kapag natapos na."),
    ("7. Transport Cost Analysis (TCAO)", "Main finance dashboard na nagco-compute ng Cost-Per-Kilometer (CPK), net profit margin, at PDF audit reports.", "Pina-process ang lahat ng datos mula sa fuel logs, trip distance, at PMS repair costs para makita ang kita at gastos.")
]

for row_data in modules_data:
    row_cells = table_modules.add_row().cells
    for i, item in enumerate(row_data):
        row_cells[i].text = item
        p = row_cells[i].paragraphs[0]
        p.runs[0].font.size = Pt(9.5)

doc.add_paragraph().paragraph_format.space_after = Pt(12)

# 3. COMPLETE KONEKSYON NG TEAM 7 SA LAHAT NG TEAMS (TEAMS 1 TO 10 & HR 1 TO 4)
add_heading_1("3. KUMPLETONG INTEGRATION SA LAHAT NG TEAMS (TEAMS 1 TO 10 & HR 1 TO 4)")
add_body_paragraph(
    "Narito ang kumpleto at detalyadong koneksyon ng Team 7 sa LAHAT ng iba pang enterprise teams sa buong sistema:"
)

table_apis = doc.add_table(rows=1, cols=3)
table_apis.alignment = WD_TABLE_ALIGNMENT.CENTER
hdr_cells2 = table_apis.rows[0].cells
headers2 = ["Enterprise Team / Module", "API Endpoint", "Paano Nagpapalitan ng Data (Integration Flow)"]
for i, head in enumerate(headers2):
    hdr_cells2[i].text = head
    set_cell_background(hdr_cells2[i], "7F1D1D")
    p = hdr_cells2[i].paragraphs[0]
    p.runs[0].font.color.rgb = RGBColor(255, 255, 255)
    p.runs[0].font.bold = True

all_teams_data = [
    ("Team 1: HR Recruitment & Onboarding", "POST /api/hr/sync-driver", "Kapag nakapasa at na-hire ang bagong driver sa Team 1 HR, automatic nitong ina-add ang driver account sa driver roster ng Team 7."),
    ("Team 2: HR Training & Certification", "POST /api/hr/sync-driver", "Ipina-pasa ang safety training status at driving certification clearance bago payagang bumiyahe ang driver sa Team 7."),
    ("Team 3: HR Attendance & Shift Roster", "POST /api/hr/sync-driver", "Ipinapasa ang daily duty shifts at attendance. Tanging ang mga driver na 'Checked-In' lang ang magiging eligible sa Team 7 Dispatch."),
    ("Team 4: HR Performance & Evaluation", "GET /api/hr/driver-performance", "Kina-kuha mula sa Team 7 ang monthly Driver Eco-Safety Scores (100% baseline), bilang ng harsh braking, at speeding events para sa HR performance review."),
    ("Team 5: Financials AP / GL Accounting", "GET /api/finance/expenses", "Ipinapasa ng Team 7 ang lahat ng resibo ng gasolina, diesel, at EV charging sa General Ledger (GL), at ang maintenance invoices sa Accounts Payable (AP)."),
    ("Team 6: Supply Chain & Inventory", "POST /api/inventory/fuel-stock\nGET /api/inventory/fuel-usage", "Kina-kuha ang live fuel inventory stock mula kay Team 6. Automatic ding nag-o-order ng Purchase Requisition (PR) para sa pyesa (gulong, langis, brake pads) kapag nag-PMS maintenance."),
    ("Team 8: Facilities & Hub Management", "GET /api/v1/facilities/*", "Kina-kuha ang eksaktong GPS coordinates ng mga terminal hubs (Manila Port, NAIA, BGC, Davao Hubs) at nagre-reserve ng parking/charging bays para sa fleet vehicles."),
    ("Team 9: Operations System", "POST /api/operations/trip-request\nGET /api/operations/vehicle-availability", "Natatanggap ang mga internal trip requests mula sa Operations at ipinapadala pabalik ang listahan ng available at active Hirna vehicles."),
    ("Team 10: CRM & Passenger Booking Portal", "POST /api/booking/assign-trip", "Natatanggap ang ride booking ng pasahero mula sa Team 10, automatic na nag-a-assign ng driver/sasakyan, at nag-e-stream ng live Leaflet GPS location at ETA pabalik sa passenger app.")
]

for row_data in all_teams_data:
    row_cells = table_apis.add_row().cells
    for i, item in enumerate(row_data):
        row_cells[i].text = item
        p = row_cells[i].paragraphs[0]
        p.runs[0].font.size = Pt(9)

doc.add_paragraph().paragraph_format.space_after = Pt(12)

# 4. STRATEGIC ADVANTAGES FOR HIRNA MOBILITY (TAGLISH)
add_heading_1("4. MGA BENEPISYO SA HIRNA MOBILITY SOLUTIONS")
add_body_paragraph("• 100% Automatic at Kumpletong Koneksyon: Nakakabit sa LAHAT ng Teams (HR 1-4, Finance 5, Inventory 6, Facilities 8, Operations 9, at Booking 10).")
add_body_paragraph("• Hiyang sa Gas, Diesel, EV, at Traysikel: Nakadisenyo para sa lahat ng uri ng biyahe—mula sa Hirna Taxis, Vans, MPVs, hanggang sa 3-wheeler Hirna Traysikel.")
add_body_paragraph("• Malinaw na Kita at Gastos (CPK): May real-time Cost-Per-KM at exportable CSV/PDF audit summaries para madaling makapagdesisyon ang pamunuan.")

# Save to public downloads and user Downloads
downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
os.makedirs(downloads_dir, exist_ok=True)
doc_path = os.path.join(downloads_dir, "Hirna_Team7_System_Architecture_and_Integration_Process.docx")
doc.save(doc_path)
print(f"Complete file updated successfully at: {doc_path}")
