import os

downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
user_downloads = os.path.expanduser(r"~\Downloads")
os.makedirs(downloads_dir, exist_ok=True)

# 1. Figure 1 - Agile Scrum Framework
fig1_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure1" name="Figure 1 - Agile Scrum Framework">
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
</mxfile>"""

# 2. Figure 2 - Burndown Chart
fig2_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure2" name="Figure 2 - Burndown Chart">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 2: SPRINT BURNDOWN CHART (100 STORY POINTS TO 0)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="s1" value="Sprint 1: 100 Points" style="ellipse;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="100" y="100" width="140" height="60" as="geometry" />
        </mxCell>
        <mxCell id="s2" value="Sprint 2: 75 Points" style="ellipse;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="300" y="180" width="140" height="60" as="geometry" />
        </mxCell>
        <mxCell id="s3" value="Sprint 4: 35 Points" style="ellipse;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="540" y="270" width="140" height="60" as="geometry" />
        </mxCell>
        <mxCell id="s4" value="Sprint 6: 0 Points (Shipped)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="780" y="360" width="180" height="60" as="geometry" />
        </mxCell>
        <mxCell id="l1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=3;" edge="1" parent="1" source="s1" target="s2" />
        <mxCell id="l2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=3;" edge="1" parent="1" source="s2" target="s3" />
        <mxCell id="l3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#10B981;strokeWidth=3;" edge="1" parent="1" source="s3" target="s4" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 3. Figure 3 - Microservices Diagram
fig3_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure3" name="Figure 3 - Microservices Architecture">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 3: HIRNA MOBILITY MICROSERVICES ARCHITECTURE" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="clients" value="CLIENT USERS &amp; DEVICES&#10;(Admins, Dispatchers, Drivers)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontStyle=1;fontSize=11;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="60" y="100" width="200" height="260" as="geometry" />
        </mxCell>
        <mxCell id="gw" value="API GATEWAY&#10;(Routing &amp; Auth)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="320" y="100" width="140" height="260" as="geometry" />
        </mxCell>
        <mxCell id="s1" value="Auth &amp; RBAC Service" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="520" y="100" width="220" height="45" as="geometry" />
        </mxCell>
        <mxCell id="s2" value="Dispatch &amp; Hub Service" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="520" y="155" width="220" height="45" as="geometry" />
        </mxCell>
        <mxCell id="s3" value="Leaflet Telemetry Engine" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="520" y="210" width="220" height="45" as="geometry" />
        </mxCell>
        <mxCell id="s4" value="AI Fuel Predictor (XGBoost)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="520" y="265" width="220" height="45" as="geometry" />
        </mxCell>
        <mxCell id="s5" value="Transport Cost Analytics (TCAO)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="520" y="320" width="220" height="45" as="geometry" />
        </mxCell>
        <mxCell id="db" value="MySQL RDBMS&#10;+ Redis Cache" style="shape=cylinder3;whiteSpace=wrap;html=1;boundedLbl=1;backgroundOutline=1;size=15;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="800" y="130" width="160" height="200" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="clients" target="gw" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="gw" target="s3" />
        <mxCell id="a3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="s3" target="db" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 4. Figure 4 - Communication Pattern
fig4_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure4" name="Figure 4 - Communication Pattern">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 4: COMMUNICATION PATTERN (REST JSON &amp; WEBSOCKET TELEMETRY)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="c1" value="Client Web Browser" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="100" y="120" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="rest" value="Synchronous HTTP REST JSON API" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="380" y="90" width="260" height="50" as="geometry" />
        </mxCell>
        <mxCell id="ws" value="Asynchronous WebSocket Telemetry Stream" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="380" y="170" width="260" height="50" as="geometry" />
        </mxCell>
        <mxCell id="srv" value="Hirna Core Services" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="720" y="120" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="c1" target="rest" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="rest" target="srv" />
        <mxCell id="a3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#F59E0B;strokeWidth=2;" edge="1" parent="1" source="c1" target="ws" />
        <mxCell id="a4" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#F59E0B;strokeWidth=2;" edge="1" parent="1" source="ws" target="srv" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 5. Figure 5 - DFD Level 0
fig5_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure5" name="Figure 5 - Data Flow Diagram Level 0">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 5: DATA FLOW DIAGRAM (DFD LEVEL 0 CONTEXT DIAGRAM)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="disp" value="Dispatcher &amp; Fleet Manager" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="80" y="150" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="sys" value="0. HIRNA MOBILITY FLEET &amp; TRANSPORTATION MANAGEMENT SYSTEM" style="ellipse;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="360" y="100" width="280" height="180" as="geometry" />
        </mxCell>
        <mxCell id="fin" value="Finance Officer &amp; Executives" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="720" y="150" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="Trip Requests &amp; Vehicle Specs" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="disp" target="sys" />
        <mxCell id="a2" value="CPK Cost Reports &amp; PDF Audits" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#10B981;strokeWidth=2;" edge="1" parent="1" source="sys" target="fin" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 6. Figure 6 - CI/CD Pipeline
fig6_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure6" name="Figure 6 - CI/CD Pipeline">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 6: AUTOMATED CI/CD PIPELINE DIAGRAM (GITHUB ACTIONS &amp; VERCEL)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="c1" value="Git Code Push&#10;(GitHub Main Branch)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="80" y="120" width="160" height="70" as="geometry" />
        </mxCell>
        <mxCell id="c2" value="GitHub Actions CI&#10;(Lint &amp; PHPUnit Tests)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="300" y="120" width="160" height="70" as="geometry" />
        </mxCell>
        <mxCell id="c3" value="Docker Build&#10;(Container Packaging)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="520" y="120" width="160" height="70" as="geometry" />
        </mxCell>
        <mxCell id="c4" value="Vercel / DigitalOcean CD&#10;(Zero-Downtime Deployment)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="740" y="120" width="180" height="70" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="c1" target="c2" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="c2" target="c3" />
        <mxCell id="a3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#10B981;strokeWidth=2;" edge="1" parent="1" source="c3" target="c4" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 7. Figure 7 - Infrastructure as Code (IaC)
fig7_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure7" name="Figure 7 - Infrastructure as Code">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 7: INFRASTRUCTURE AS A CODE (IaC DOCKER CONTAINERS)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="dc" value="DOCKER COMPOSE ORCHESTRATION ENGINE" style="swimlane;startSize=30;horizontal=1;fontStyle=1;fontSize=12;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="100" y="90" width="840" height="240" as="geometry" />
        </mxCell>
        <mxCell id="cont1" value="Nginx Web Server Container" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="dc">
          <mxGeometry x="30" y="60" width="170" height="120" as="geometry" />
        </mxCell>
        <mxCell id="cont2" value="Laravel PHP 8.2 Runtime Container" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="dc">
          <mxGeometry x="230" y="60" width="170" height="120" as="geometry" />
        </mxCell>
        <mxCell id="cont3" value="MySQL RDBMS Data Container" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="dc">
          <mxGeometry x="430" y="60" width="170" height="120" as="geometry" />
        </mxCell>
        <mxCell id="cont4" value="Redis Key-Value Cache Container" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;" vertex="1" parent="dc">
          <mxGeometry x="630" y="60" width="170" height="120" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 8. Figure 8 - Monitoring and Alerting
fig8_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure8" name="Figure 8 - Monitoring and Alerting">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 8: TELEMETRY MONITORING &amp; DISPATCHER ALERTING ARCHITECTURE" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="stream" value="Real-Time Telematics Stream&#10;(GPS, Speed km/h, Idling)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="80" y="130" width="180" height="80" as="geometry" />
        </mxCell>
        <mxCell id="check" value="Safety Threshold Check&#10;(Speed &gt; 80 km/h? Harsh Brake?)" style="rhombus;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="320" y="100" width="220" height="140" as="geometry" />
        </mxCell>
        <mxCell id="alt1" value="Trigger Dispatcher Speed Alert &amp; Deduct Driver Score" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="600" y="90" width="260" height="60" as="geometry" />
        </mxCell>
        <mxCell id="alt2" value="Log Normal Telemetry &amp; Update Map Trail" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="600" y="190" width="260" height="60" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="stream" target="check" />
        <mxCell id="a2" value="Violation" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="check" target="alt1" />
        <mxCell id="a3" value="Normal" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#10B981;strokeWidth=2;" edge="1" parent="1" source="check" target="alt2" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 9. Figure 9 - Integration Diagram
fig9_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure9" name="Figure 9 - Enterprise Integration Diagram">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 9: ENTERPRISE SYSTEM INTEGRATION DIAGRAM (HRMS, PAYROLL, CRM)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="core" value="HIRNA MOBILITY FLEET PLATFORM" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="80" y="100" width="220" height="240" as="geometry" />
        </mxCell>
        <mxCell id="api1" value="/api/v1/hrms/drivers" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="380" y="110" width="180" height="50" as="geometry" />
        </mxCell>
        <mxCell id="api2" value="/api/v1/payroll/safety-bonus" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="380" y="195" width="180" height="50" as="geometry" />
        </mxCell>
        <mxCell id="api3" value="/api/v1/crm/dispatches" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="380" y="280" width="180" height="50" as="geometry" />
        </mxCell>
        <mxCell id="sys1" value="HRMS System (Team 1-4)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="640" y="110" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="sys2" value="Payroll System (Team 4)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="640" y="195" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="sys3" value="CRM System (Team 10)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="640" y="280" width="200" height="50" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="core" target="api1" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#10B981;strokeWidth=2;" edge="1" parent="1" source="api1" target="sys1" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# 10. Figure 10 - API Gateway
fig10_xml = """<mxfile host="Electron" modified="2026-08-30T20:42:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure10" name="Figure 10 - API Gateway Diagram">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <mxCell id="title" value="FIGURE 10: API GATEWAY SECURITY &amp; ROUTING MIDDLEWARE PATTERN" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="180" y="20" width="800" height="40" as="geometry" />
        </mxCell>
        <mxCell id="req" value="Incoming HTTP Request" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="60" y="130" width="160" height="60" as="geometry" />
        </mxCell>
        <mxCell id="gw" value="API GATEWAY" style="swimlane;startSize=30;horizontal=1;fontStyle=1;fontSize=12;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="260" y="90" width="460" height="140" as="geometry" />
        </mxCell>
        <mxCell id="m1" value="Token Verifier" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="gw">
          <mxGeometry x="20" y="50" width="120" height="60" as="geometry" />
        </mxCell>
        <mxCell id="m2" value="Rate Limiter" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="gw">
          <mxGeometry x="170" y="50" width="120" height="60" as="geometry" />
        </mxCell>
        <mxCell id="m3" value="Route Dispatcher" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="gw">
          <mxGeometry x="320" y="50" width="120" height="60" as="geometry" />
        </mxCell>
        <mxCell id="backend" value="Target Microservice Endpoint" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="760" y="130" width="180" height="60" as="geometry" />
        </mxCell>
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#CE2029;strokeWidth=2;" edge="1" parent="1" source="req" target="gw" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;strokeColor=#10B981;strokeWidth=2;" edge="1" parent="1" source="gw" target="backend" />
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

files_map = {
    "Figure1_Agile_Scrum_Framework.drawio": fig1_xml,
    "Figure2_Burndown_Chart.drawio": fig2_xml,
    "Figure3_Microservices_Diagram.drawio": fig3_xml,
    "Figure4_Communication_Pattern.drawio": fig4_xml,
    "Figure5_Data_Flow_Diagram_Level0.drawio": fig5_xml,
    "Figure6_CICD_Pipeline.drawio": fig6_xml,
    "Figure7_Infrastructure_as_Code.drawio": fig7_xml,
    "Figure8_Monitoring_and_Alerting.drawio": fig8_xml,
    "Figure9_Integration_Diagram.drawio": fig9_xml,
    "Figure10_API_Gateway.drawio": fig10_xml
}

for fname, xml_data in files_map.items():
    p_pub = os.path.join(downloads_dir, fname)
    p_usr = os.path.join(user_downloads, fname)
    with open(p_pub, "w", encoding="utf-8") as f: f.write(xml_data)
    with open(p_usr, "w", encoding="utf-8") as f: f.write(xml_data)

print(f"Successfully generated all {len(files_map)} individual Draw.io diagram files!")
