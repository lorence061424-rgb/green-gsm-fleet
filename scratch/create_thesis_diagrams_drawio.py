import os

downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
user_downloads = os.path.expanduser(r"~\Downloads")
os.makedirs(downloads_dir, exist_ok=True)

# 1. Figure 1 & 2: Agile Scrum & Burndown Diagram
drawio_scrum = """<mxfile host="Electron" modified="2026-08-30T20:35:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="AgileScrumFramework" name="Figure 1 - Agile Scrum Framework">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 1: HIRNA MOBILITY ADAPTED AGILE SCRUM FRAMEWORK" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="pb" value="PRODUCT BACKLOG&#10;(Prioritized User Stories &amp; API Features)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="80" y="100" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="sb" value="SPRINT BACKLOG&#10;(2-Week Committed Tasks)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="320" y="100" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="sc" value="SPRINT CYCLE (2 WEEKS)&#10;• Daily Stand-up (15 mins)&#10;• Laravel/Leaflet Dev&#10;• ML Model Training" style="ellipse;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="560" y="75" width="210" height="130" as="geometry" />
        </mxCell>
        <mxCell id="inc" value="POTENTIALLY SHIPPABLE INCREMENT&#10;(Tested Portal Module)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="840" y="100" width="200" height="80" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="pb" target="sb" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="sb" target="sc" />
        <mxCell id="a3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="sc" target="inc" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

# 2. Figure 3, 4, 9, 10: Microservices, Integration & API Gateway
drawio_microservices = """<mxfile host="Electron" modified="2026-08-30T20:35:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="MicroservicesArchitecture" name="Figure 3 - Microservices Architecture">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 3 &amp; 10: HIRNA MOBILITY MICROSERVICES ARCHITECTURE &amp; API GATEWAY" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="clients" value="CLIENT USERS &amp; DEVICES&#10;(Admins, Dispatchers, Finance Officers, Drivers)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontStyle=1;fontSize=11;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="60" y="100" width="220" height="300" as="geometry" />
        </mxCell>
        <mxCell id="gw" value="API GATEWAY &amp; REVERSE PROXY&#10;(Route Handler • Token Auth • Rate Limiting)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="340" y="100" width="160" height="300" as="geometry" />
        </mxCell>
        <mxCell id="s1" value="AUTH &amp; RBAC SERVICE&#10;(Bcrypt / JWT Security)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="560" y="100" width="220" height="50" as="geometry" />
        </mxCell>
        <mxCell id="s2" value="DISPATCH &amp; HUB SERVICE&#10;(Vehicle-Driver Auto Pairing)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="560" y="165" width="220" height="50" as="geometry" />
        </mxCell>
        <mxCell id="s3" value="LEAFLET TELEMETRY SERVICE&#10;(Live GPS &amp; Speeding Alerts)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="560" y="230" width="220" height="50" as="geometry" />
        </mxCell>
        <mxCell id="s4" value="AI FUEL PREDICTOR SERVICE&#10;(XGBoost / Random Forest ML)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="560" y="295" width="220" height="50" as="geometry" />
        </mxCell>
        <mxCell id="s5" value="TRANSPORT COST ANALYTICS (TCAO)&#10;(CPK &amp; Profit Margin Engine)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="560" y="360" width="220" height="50" as="geometry" />
        </mxCell>
        <mxCell id="db" value="POLYGLOT PERSISTENCE&#10;MySQL RDBMS + Redis Cache" style="shape=cylinder3;whiteSpace=wrap;html=1;boundedLbl=1;backgroundOutline=1;size=15;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="840" y="150" width="180" height="200" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="clients" target="gw" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="gw" target="s3" />
        <mxCell id="a3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="s3" target="db" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

# 3. Figure 5, 11, 12, 13, 14: Use Case, Flowchart & DFD Diagram
drawio_usecase_flowchart = """<mxfile host="Electron" modified="2026-08-30T20:35:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="UseCaseDiagram" name="Figure 12 - Use Case Diagram">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 12: HIRNA MOBILITY FLEET SYSTEM USE CASE DIAGRAM" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="actor_admin" value="System Administrator" style="shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;outlineConnect=0;fontStyle=1;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="80" y="100" width="40" height="80" as="geometry" />
        </mxCell>
        <mxCell id="actor_fm" value="Fleet Manager" style="shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;outlineConnect=0;fontStyle=1;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="80" y="230" width="40" height="80" as="geometry" />
        </mxCell>
        <mxCell id="actor_disp" value="Dispatcher" style="shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;outlineConnect=0;fontStyle=1;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="80" y="360" width="40" height="80" as="geometry" />
        </mxCell>
        <mxCell id="actor_fin" value="Finance Officer" style="shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;outlineConnect=0;fontStyle=1;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="1040" y="140" width="40" height="80" as="geometry" />
        </mxCell>
        <mxCell id="actor_driver" value="Driver" style="shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;outlineConnect=0;fontStyle=1;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="1040" y="320" width="40" height="80" as="geometry" />
        </mxCell>
        <mxCell id="sys_box" value="HIRNA MOBILITY FLEET PLATFORM" style="swimlane;startSize=30;horizontal=1;fontStyle=1;fontSize=12;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="200" y="80" width="760" height="420" as="geometry" />
        </mxCell>
        <mxCell id="uc1" value="Manage Users &amp; Roles (RBAC)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="40" y="45" width="180" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc2" value="Manage Fleet Inventory (Taxis, EVs)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="40" y="115" width="180" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc3" value="Dispatch Vehicle &amp; Override Pairing" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="40" y="185" width="180" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc4" value="Track Leaflet Live GPS Telemetry" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="280" y="115" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc5" value="Predict AI Fuel/Energy Burn (kWh/L)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="280" y="185" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc6" value="Monitor Driver Eco-Safety Scores" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="280" y="255" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc7" value="Generate CPK Transport Cost Dashboard" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="530" y="115" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="uc8" value="View Assigned Schedule &amp; Scorecard" style="ellipse;whiteSpace=wrap;html=1;fillColor=#FEF2F2;strokeColor=#CE2029;fontStyle=1;fontSize=10;" vertex="1" parent="sys_box">
          <mxGeometry x="530" y="255" width="200" height="50" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""

# Save drawio files
for path in [os.path.join(downloads_dir, "Figure1_Agile_Scrum_Framework.drawio"), os.path.join(user_downloads, "Figure1_Agile_Scrum_Framework.drawio")]:
    with open(path, "w", encoding="utf-8") as f: f.write(drawio_scrum)

for path in [os.path.join(downloads_dir, "Figure3_10_Microservices_API_Gateway.drawio"), os.path.join(user_downloads, "Figure3_10_Microservices_API_Gateway.drawio")]:
    with open(path, "w", encoding="utf-8") as f: f.write(drawio_microservices)

for path in [os.path.join(downloads_dir, "Figure12_UseCase_Diagram.drawio"), os.path.join(user_downloads, "Figure12_UseCase_Diagram.drawio")]:
    with open(path, "w", encoding="utf-8") as f: f.write(drawio_usecase_flowchart)

print("All Draw.io diagrams generated successfully!")
