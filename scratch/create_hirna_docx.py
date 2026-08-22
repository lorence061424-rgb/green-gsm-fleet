import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
import os

doc = docx.Document()

# Set standard margins (1 inch)
sections = doc.sections
for section in sections:
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)

# Color Palette: Hirna Crimson Red (#CE2029), Dark Crimson (#7F1D1D), Sun Gold (#F59E0B)
PRIMARY_COLOR = RGBColor(206, 32, 41)
SECONDARY_COLOR = RGBColor(127, 29, 29)
GOLD_COLOR = RGBColor(245, 158, 11)
DARK_TEXT = RGBColor(40, 40, 40)

# Helper function to set table cell background color
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

run_title = title_p.add_run("SYSTEM ARCHITECTURE & MODULE INTEGRATION PROCESS")
run_title.font.name = 'Arial'
run_title.font.size = Pt(20)
run_title.font.bold = True
run_title.font.color.rgb = PRIMARY_COLOR

subtitle_p = doc.add_paragraph()
subtitle_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_sub2 = subtitle_p.add_run("Team 7: Fleet and Transportation Management System with AI-Based Fuel Consumption Prediction and Transport Cost Analysis\n")
run_sub2.font.name = 'Arial'
run_sub2.font.size = Pt(11)
run_sub2.font.italic = True
run_sub2.font.color.rgb = SECONDARY_COLOR

doc.add_paragraph().paragraph_format.space_after = Pt(12)

# Heading 1 Styling Function
def add_heading_1(text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(18)
    h.paragraph_format.space_after = Pt(6)
    run = h.add_run(text)
    run.font.name = 'Arial'
    run.font.size = Pt(14)
    run.font.bold = True
    run.font.color.rgb = PRIMARY_COLOR
    return h

# Heading 2 Styling Function
def add_heading_2(text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(12)
    h.paragraph_format.space_after = Pt(4)
    run = h.add_run(text)
    run.font.name = 'Arial'
    run.font.size = Pt(12)
    run.font.bold = True
    run.font.color.rgb = SECONDARY_COLOR
    return h

# Body Paragraph Function
def add_body_paragraph(text):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    run.font.name = 'Arial'
    run.font.size = Pt(11)
    run.font.color.rgb = DARK_TEXT
    return p

# 1. EXECUTIVE SUMMARY
add_heading_1("1. EXECUTIVE SUMMARY & OVERALL SYSTEM PROCESS")
add_body_paragraph(
    "The Fleet and Transportation Management System (Team 7) for Hirna Mobility Solutions Inc. operates as a unified, "
    "closed-loop enterprise platform. It automates the entire vehicle lifecycle—from driver onboarding and vehicle dispatch "
    "to live Leaflet GPS telematics tracking, AI-based multi-fuel prediction, preventive maintenance logging, and real-time "
    "Transport Cost Analysis & Optimization (TCAO)."
)

add_body_paragraph(
    "By continuously synthesizing live telemetry streams with historical vehicle databases, the system replaces manual "
    "spreadsheet tracking with automated data pipelines. It supports multi-fuel operations, including Gasoline (Gas), Diesel, "
    "and Electric Vehicles (EVs), as well as Hirna Traysikel 3-wheelers across Metro Manila and Davao transit corridors."
)

# 2. INTERNAL MODULE CONNECTIONS WITHIN TEAM 7
add_heading_1("2. INTERNAL MODULE INTERCONNECTIONS (TEAM 7)")
add_body_paragraph(
    "Team 7 consists of seven core modules that communicate dynamically to ensure operational transparency and financial precision:"
)

table_modules = doc.add_table(rows=1, cols=3)
table_modules.alignment = WD_TABLE_ALIGNMENT.CENTER
hdr_cells = table_modules.rows[0].cells
headers = ["Module Name", "Primary Function", "Internal System Connections"]
for i, head in enumerate(headers):
    hdr_cells[i].text = head
    set_cell_background(hdr_cells[i], "CE2029")
    p = hdr_cells[i].paragraphs[0]
    p.runs[0].font.color.rgb = RGBColor(255, 255, 255)
    p.runs[0].font.bold = True

modules_data = [
    ("1. Fleet & Vehicle Management (FVM)", "Central registry for vehicles (Sedans, SUVs, Vans, Traysikel, EVs) & driver profiles.", "Feeds vehicle status (Active, Maintenance, Offline) to Reservation & Dispatch."),
    ("2. Vehicle Reservation & Dispatch (VRD)", "Pairs available vehicles with drivers and manages 2-step reservation approvals.", "Sends trip coordinates and Booking Reference IDs to Route Planning and Live GPS Telemetry."),
    ("3. Route Planning & Optimization (RPO)", "Calculates 3 eco-route options (Eco, Highway, City Bypass) for Gas, Diesel, & EV.", "Sends estimated distance and traffic congestion levels to the AI Fuel Predictor engine."),
    ("4. Driver & Trip Performance (DTPM)", "Tracks live GPS speed, idling time, and penalizes driver safety scores (100% baseline).", "Triggers maintenance alerts to PMS when recurring speeding or harsh braking events occur."),
    ("5. Fuel Management System (FMS)", "Logs refueling/charging events, stores unit prices (₱/L or ₱/kWh), and trains ML model.", "Feeds real-time fuel and energy expenditure data to the Transport Cost Analysis engine."),
    ("6. Preventive Maintenance (PMS)", "Schedules tune-ups, logs repair expenses, and manages service statuses.", "Automatically updates vehicle status to 'Maintenance' when service begins, and 'Active' when completed."),
    ("7. Transport Cost Analysis (TCAO)", "Central financial dashboard calculating Cost-Per-KM (CPK), net profits, and PDF audits.", "Aggregates fuel logs, trip distances, and PMS repair costs from all upstream modules.")
]

for row_data in modules_data:
    row_cells = table_modules.add_row().cells
    for i, item in enumerate(row_data):
        row_cells[i].text = item
        p = row_cells[i].paragraphs[0]
        p.runs[0].font.size = Pt(10)

doc.add_paragraph().paragraph_format.space_after = Pt(12)

# 3. EXTERNAL INTER-SYSTEM INTEGRATION PIPELINES
add_heading_1("3. EXTERNAL PEER INTEGRATION PIPELINES (TEAMS 5, 6, 8, 9, & 10)")
add_body_paragraph(
    "Team 7 integrates seamlessly with five peer enterprise systems via automated RESTful APIs (/api/v1/*) to enable end-to-end enterprise synchronization:"
)

table_apis = doc.add_table(rows=1, cols=3)
table_apis.alignment = WD_TABLE_ALIGNMENT.CENTER
hdr_cells2 = table_apis.rows[0].cells
headers2 = ["Peer System", "API Endpoint", "Integration Workflow & Data Exchange"]
for i, head in enumerate(headers2):
    hdr_cells2[i].text = head
    set_cell_background(hdr_cells2[i], "7F1D1D")
    p = hdr_cells2[i].paragraphs[0]
    p.runs[0].font.color.rgb = RGBColor(255, 255, 255)
    p.runs[0].font.bold = True

api_data = [
    ("Team 9: HRMS & Payroll", "/api/v1/hrms/*\n/api/v1/payroll/*", "Verifies driver licenses and employment status. Exports monthly Driver Eco-Safety Scores (100% baseline) for payroll performance bonuses or penalties."),
    ("Team 10: CRM & Passenger Portal", "/api/v1/crm/*", "Receives passenger booking requests from Team 10, pairs an available driver/vehicle, and pushes live trip dispatch status back to the CRM."),
    ("Team 5: Financials AP/GL", "/api/v1/finance/*", "Pushes all logged fuel purchases (Gas/Diesel) and EV charging expenses to Team 5 General Ledger (GL). Sends maintenance invoices to Accounts Payable (AP)."),
    ("Team 6: Supply Chain (PR)", "/api/v1/supply-chain/*", "Automatically generates Purchase Requisitions (PR) for replacement vehicle spare parts (brake pads, oil, tires) when PMS maintenance is scheduled."),
    ("Team 8: Facilities & Hubs", "/api/v1/facilities/*", "Syncs transit hub coordinates (Manila Port, NAIA, BGC, Davao Hubs) and reserves depot charging station bays for fleet vehicles.")
]

for row_data in api_data:
    row_cells = table_apis.add_row().cells
    for i, item in enumerate(row_data):
        row_cells[i].text = item
        p = row_cells[i].paragraphs[0]
        p.runs[0].font.size = Pt(10)

doc.add_paragraph().paragraph_format.space_after = Pt(12)

# 4. STRATEGIC ADVANTAGES FOR HIRNA MOBILITY
add_heading_1("4. STRATEGIC SYSTEM ADVANTAGES FOR HIRNA MOBILITY")
add_body_paragraph("• 100% Dynamic Operations: Eliminates manual spreadsheets through automated RESTful API pipelines and live database updates.")
add_body_paragraph("• Multi-Fuel & 3-Wheeler Versatility: Supports Taxis (Gasoline/Diesel), VinFast EVs, and Hirna Traysikel 3-wheelers with custom fuel consumption multipliers.")
add_body_paragraph("• Data-Driven Cost Optimization: Equips managers with real-time Cost-Per-KM (CPK), trip profit margins, and exportable CSV/PDF audit summaries.")

# Save to public downloads folder
downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
os.makedirs(downloads_dir, exist_ok=True)
doc_path = os.path.join(downloads_dir, "Hirna_Team7_System_Architecture_and_Integration_Process.docx")
doc.save(doc_path)
print(f"File created successfully at: {doc_path}")
