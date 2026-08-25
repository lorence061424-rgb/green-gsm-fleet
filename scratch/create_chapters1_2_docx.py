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

def add_body_paragraph(text):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    run.font.name = 'Arial'
    run.font.size = Pt(10.5)
    run.font.color.rgb = DARK_TEXT
    return p

# Section 2.8 Theoretical Paradigm Content
add_heading_2("2.8 Theoretical Paradigm")

add_body_paragraph(
    "The proposed Design and Development of a Fleet and Transportation Management System for Hirna Mobility Solutions with "
    "AI-Based Fuel Consumption Prediction and Transport Cost Analysis is anchored in theories, frameworks, and software standards "
    "that explain information management, decision support, machine learning predictive analytics, vehicle telematics, and software quality. "
    "The theoretical paradigm provides the foundational bedrock for transforming manual, spreadsheet-based fleet administration processes "
    "into a centralized, intelligent, and web-supported transportation management ecosystem."
)

add_body_paragraph(
    "1. Information Systems Theory\n"
    "Information Systems Theory (Laudon & Laudon, 2020) provides the primary foundation of the proposed system because the project involves "
    "collecting, processing, storing, retrieving, and presenting vehicle fleet information. Operational data—such as vehicle inventory profiles, "
    "driver rosters, terminal hub coordinates, fuel refill receipts, and trip dispatch logs—serve as structural inputs that are processed and "
    "transformed into actionable managerial intelligence for dispatchers, operations managers, and finance officers. The theory supports the "
    "development of a centralized platform that improves organizational efficiency, record accessibility, and decision-making across Hirna Mobility Solutions Inc."
)

add_body_paragraph(
    "2. Decision Support System (DSS) Theory\n"
    "Decision Support System (DSS) Theory provides the conceptual foundation for the predictive and analytical components of the system. "
    "A decision support system assists human decision-makers by processing dynamic information, calculating multi-variable models, and presenting "
    "actionable recommendations without completely replacing human administrative control (Power, 2019). In the proposed system, the AI-based fuel "
    "prediction engine analyzes pre-dispatch variables—such as route distance, vehicle engine specifications, dynamic traffic congestion levels, "
    "and road gradients—to generate pre-dispatch fuel consumption estimates (in Liters or kWh) and cost-per-kilometer (CPK) projections. "
    "These predictions serve as decision-support information to assist fleet managers and finance officers in evaluating trip feasibility, "
    "optimizing vehicle selection, and detecting expense anomalies."
)

add_body_paragraph(
    "3. Algorithmic Machine Learning & Predictive Analytics Theory\n"
    "Algorithmic Machine Learning Theory provides the mathematical and computational foundation for data-driven forecasting in fleet logistics. "
    "Supervised learning algorithms (such as XGBoost, Random Forest, and Multiple Linear Regression) identify non-linear relationships within "
    "historical telemetry datasets to predict vehicle energy burn (Hastie et al., 2017). Applied to the proposed system, machine learning "
    "algorithms continuously refine their predictive baselines through iterative training on empirical trip logs. This enables the platform to "
    "forecast fuel burn and EV battery depletion under fluctuating traffic congestion conditions across Metro Manila and Davao transit corridors."
)

add_body_paragraph(
    "4. Vehicle Telematics & IoT Framework\n"
    "Vehicle Telematics Framework provides the technical foundation for real-time remote monitoring of mobile transportation assets. "
    "Telematics integrates telecommunications, informatics, and web-based geographic information systems (GIS) to transmit vehicle location, "
    "velocity, idling time, and deceleration signals across wireless networks (Bhardwaj et al., 2021). In the proposed system, software-simulated "
    "IoT telematics streams continuously feed live latitude/longitude coordinates, speed metrics (km/h), and safety event flags to an interactive "
    "Leaflet.js GPS mapping engine, enabling real-time visual tracking, progressive breadcrumb trails, and automated driver eco-safety scoring."
)

add_body_paragraph(
    "5. ISO/IEC 25010 Software Quality Product Evaluation Model\n"
    "The ISO/IEC 25010 Software Quality Model provides the standardized criteria for evaluating the technical performance and usability of the "
    "developed Hirna fleet management platform. The model establishes eight essential software quality characteristics: Functional Suitability, "
    "Performance Efficiency, Compatibility, Usability, Reliability, Security, Maintainability, and Portability (ISO/IEC, 2011). In this study, "
    "Functional Suitability evaluates whether the system accurately handles dispatching, telematics, and cost analysis; Security assesses role-based "
    "access control (RBAC) and compliance with the Philippine Data Privacy Act (RA 10173); Performance Efficiency measures page rendering and "
    "API response times; and Usability evaluates user satisfaction among Hirna administrators, dispatchers, finance officers, and drivers."
)

add_body_paragraph(
    "Theoretical Paradigm Model Diagram:\n"
    "+----------------------------------------------------------------------------------------------------+\n"
    "|                                    THEORETICAL PARADIGM MODEL                                     |\n"
    "+----------------------------------------------------------------------------------------------------+\n"
    "|  [1. Information Systems Theory] ──► Centralized Fleet Data Storage, Processing & Retrieval     |\n"
    "|  [2. Decision Support System]    ──► Pre-Dispatch AI Guidance & Operational Cost Alerts           |\n"
    "|  [3. Algorithmic Machine Learning]─► XGBoost/Random Forest Multi-Fuel Consumption Predictor        |\n"
    "|  [4. Vehicle Telematics & IoT]   ──► Live Leaflet GPS Tracking, Idling & Eco-Safety Scoring       |\n"
    "|  [5. ISO/IEC 25010 Quality Model]──► Standardized Evaluation (Functional, Security & Usability)  |\n"
    "+--------------------------------─────────────────┬--------------------------------------------------+\n"
    "                                                  │ Supports & Guides\n"
    "                                                  ▼\n"
    "+----------------------------------------------------------------------------------------------------+\n"
    "|                   HIRNA MOBILITY FLEET & TRANSPORTATION MANAGEMENT SYSTEM                          |\n"
    "|      (Vehicle Dispatch • Leaflet GPS Telemetry • AI Fuel Prediction • Transport Cost Analysis)     |\n"
    "+------------------------------------------------─┬--------------------------------------------------+\n"
    "                                                  │ Yields Operational Impact\n"
    "                                                  ▼\n"
    "+----------------------------------------------------------------------------------------------------+\n"
    "|                                      SYSTEM OUTCOMES & IMPACT                                      |\n"
    "|  • Elimination of Unmonitored Fuel Waste & EV Battery Drain                                        |\n"
    "|  • Automated Cost-Per-Kilometer (CPK) & Net Profit Margin Analytics                                |\n"
    "|  • Standardized Driver Eco-Safety Scores & Incident Alert Monitoring                               |\n"
    "|  • Enterprise Inter-System Data Synchronization (HRMS, Payroll, CRM APIs)                          |\n"
    "+----------------------------------------------------------------------------------------------------+"
)

add_body_paragraph(
    "Paradigm Explanation:\n"
    "The theoretical paradigm begins with Information Systems Theory, which establishes the structural foundation for collecting, processing, and "
    "organizing fleet vehicle profiles, driver rosters, and trip dispatches. Decision Support System (DSS) Theory explains how processed telemetry "
    "and predictive machine learning algorithms transform raw operational data into pre-dispatch AI energy forecasts and cost-per-kilometer recommendations. "
    "Algorithmic Machine Learning Theory and Vehicle Telematics Framework provide the underlying computational mechanisms for training regression models on "
    "fuel burn and visualizing live Leaflet GPS telemetry streams. Finally, the ISO/IEC 25010 Software Quality Model provides the standardized evaluation "
    "framework to verify that the resulting system is functional, secure, efficient, reliable, and user-friendly for all Hirna Mobility stakeholders. "
    "Together, these five theoretical pillars support the development of a Fleet and Transportation Management System that replaces manual spreadsheet "
    "tracking with an automated, data-driven operational platform."
)

# Save to public downloads and user Downloads
downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
os.makedirs(downloads_dir, exist_ok=True)
doc_path = os.path.join(downloads_dir, "Hirna_Capstone_Chapter1_and_Chapter2.docx")
doc.save(doc_path)
print(f"Updated Section 2.8 Word document created successfully at: {doc_path}")
