import os

downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
user_downloads = os.path.expanduser(r"~\Downloads")
os.makedirs(downloads_dir, exist_ok=True)

# High-precision Draw.io XML for Figure 3: Microservices Diagram with ALL explicit directional arrows
fig3_detailed_xml = """<mxfile host="Electron" modified="2026-08-30T23:31:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure3_Detailed" name="Figure 3 - Microservices Architecture">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        
        <!-- Main Title Banner -->
        <mxCell id="title" value="FIGURE 3: HIRNA MOBILITY MICROSERVICES ARCHITECTURE &amp; DATA PIPELINE" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="120" y="20" width="940" height="40" as="geometry" />
        </mxCell>

        <!-- 1. Client Applications Tier -->
        <mxCell id="client_box" value="CLIENT USER APPLICATIONS&#10;(Role-Based Web Portals)" style="swimlane;startSize=25;horizontal=1;fontStyle=1;fontSize=11;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="40" y="90" width="180" height="380" as="geometry" />
        </mxCell>
        <mxCell id="c1" value="Admin &amp; Manager&#10;Dashboard" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="client_box">
          <mxGeometry x="15" y="40" width="150" height="50" as="geometry" />
        </mxCell>
        <mxCell id="c2" value="Dispatcher Command&#10;&amp; Telematics View" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="client_box">
          <mxGeometry x="15" y="110" width="150" height="50" as="geometry" />
        </mxCell>
        <mxCell id="c3" value="Finance Officer&#10;TCAO Cost Portal" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="client_box">
          <mxGeometry x="15" y="180" width="150" height="50" as="geometry" />
        </mxCell>
        <mxCell id="c4" value="Driver Mobile&#10;Web Interface" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="client_box">
          <mxGeometry x="15" y="250" width="150" height="50" as="geometry" />
        </mxCell>
        <mxCell id="c5" value="IoT Vehicle Telematics&#10;GPS Stream" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontSize=10;fontStyle=1;" vertex="1" parent="client_box">
          <mxGeometry x="15" y="315" width="150" height="50" as="geometry" />
        </mxCell>

        <!-- 2. Central API Gateway Tier -->
        <mxCell id="gw_box" value="CENTRAL API GATEWAY&#10;(Nginx &amp; Middleware)" style="swimlane;startSize=25;horizontal=1;fontStyle=1;fontSize=11;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="270" y="90" width="170" height="380" as="geometry" />
        </mxCell>
        <mxCell id="gw_ssl" value="• SSL/TLS Termination&#10;• JWT/OAuth2 Auth&#10;• Rate Limiting&#10;• Request Routing&#10;• CORS &amp; CSRF Filter&#10;• Load Balancing" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontSize=11;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="gw_box">
          <mxGeometry x="15" y="50" width="140" height="300" as="geometry" />
        </mxCell>

        <!-- 3. Microservices Core Tier -->
        <mxCell id="ms_box" value="HIRNA BACKEND MICROSERVICES" style="swimlane;startSize=25;horizontal=1;fontStyle=1;fontSize=11;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="490" y="90" width="280" height="380" as="geometry" />
        </mxCell>
        <mxCell id="ms1" value="1. Auth &amp; RBAC Security Service&#10;(Role permissions, bcrypt tokens)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="ms_box">
          <mxGeometry x="20" y="40" width="240" height="50" as="geometry" />
        </mxCell>
        <mxCell id="ms2" value="2. Vehicle &amp; Dispatch Service&#10;(Roster, hub schedules, pairing)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="ms_box">
          <mxGeometry x="20" y="105" width="240" height="50" as="geometry" />
        </mxCell>
        <mxCell id="ms3" value="3. Leaflet GPS Telemetry Engine&#10;(Live coordinates, pulsing radar aura)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="ms_box">
          <mxGeometry x="20" y="170" width="240" height="50" as="geometry" />
        </mxCell>
        <mxCell id="ms4" value="4. AI Fuel &amp; Energy Predictor&#10;(XGBoost / Random Forest Models)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontSize=10;fontStyle=1;" vertex="1" parent="ms_box">
          <mxGeometry x="20" y="235" width="240" height="50" as="geometry" />
        </mxCell>
        <mxCell id="ms5" value="5. Transport Cost Analytics (TCAO)&#10;(CPK ₱/km, profit margins, PDF audits)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="ms_box">
          <mxGeometry x="20" y="300" width="240" height="50" as="geometry" />
        </mxCell>

        <!-- 4. Polyglot Persistence Tier -->
        <mxCell id="db_box" value="DATA STORAGE &amp; CACHING TIER" style="swimlane;startSize=25;horizontal=1;fontStyle=1;fontSize=11;fillColor=#FFF8F8;strokeColor=#10B981;fontColor=#047857;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="820" y="90" width="240" height="210" as="geometry" />
        </mxCell>
        <mxCell id="db_mysql" value="MySQL / PostgreSQL RDBMS&#10;(Transactional Tables: Users, Vehicles,&#10;Dispatches, Fuel Receipts, Financial Logs)" style="shape=cylinder3;whiteSpace=wrap;html=1;boundedLbl=1;backgroundOutline=1;size=10;fillColor=#10B981;fontColor=#FFFFFF;fontSize=10;fontStyle=1;" vertex="1" parent="db_box">
          <mxGeometry x="15" y="35" width="210" height="75" as="geometry" />
        </mxCell>
        <mxCell id="db_redis" value="Redis In-Memory Cache&#10;(High-Frequency Live GPS Lat/Long,&#10;Active WebSockets, Map Sessions)" style="shape=cylinder3;whiteSpace=wrap;html=1;boundedLbl=1;backgroundOutline=1;size=10;fillColor=#F59E0B;fontColor=#000000;fontSize=10;fontStyle=1;" vertex="1" parent="db_box">
          <mxGeometry x="15" y="120" width="210" height="75" as="geometry" />
        </mxCell>

        <!-- 5. Enterprise Peer Systems Integration Tier -->
        <mxCell id="ext_box" value="ENTERPRISE API INTEGRATIONS" style="swimlane;startSize=25;horizontal=1;fontStyle=1;fontSize=11;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;rounded=1;" vertex="1" parent="1">
          <mxGeometry x="820" y="315" width="240" height="155" as="geometry" />
        </mxCell>
        <mxCell id="ext_hrms" value="HRMS Platform (/api/v1/hrms/*)&#10;Driver license &amp; credentials sync" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=9;fontStyle=1;" vertex="1" parent="ext_box">
          <mxGeometry x="15" y="30" width="210" height="35" as="geometry" />
        </mxCell>
        <mxCell id="ext_payroll" value="Payroll System (/api/v1/payroll/*)&#10;Driver eco-safety bonus/penalty export" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=9;fontStyle=1;" vertex="1" parent="ext_box">
          <mxGeometry x="15" y="70" width="210" height="35" as="geometry" />
        </mxCell>
        <mxCell id="ext_crm" value="CRM Platform (/api/v1/crm/*)&#10;Live booking ETA &amp; dispatch feeds" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontSize=9;fontStyle=1;" vertex="1" parent="ext_box">
          <mxGeometry x="15" y="110" width="210" height="35" as="geometry" />
        </mxCell>

        <!-- ARROWS: Clients to API Gateway -->
        <mxCell id="arr_c_gw" value="HTTPS REST / WS" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;fontColor=#7F1D1D;fontStyle=1;fontSize=9;" edge="1" parent="1" source="client_box" target="gw_box" />

        <!-- ARROWS: API Gateway to 5 Backend Microservices -->
        <mxCell id="arr_gw_ms1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_box" target="ms1" />
        <mxCell id="arr_gw_ms2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_box" target="ms2" />
        <mxCell id="arr_gw_ms3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_box" target="ms3" />
        <mxCell id="arr_gw_ms4" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_box" target="ms4" />
        <mxCell id="arr_gw_ms5" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_box" target="ms5" />

        <!-- ARROWS: Microservices to MySQL RDBMS -->
        <mxCell id="arr_ms_mysql" value="ACID Transactions" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#10B981;strokeWidth=2;fontColor=#047857;fontStyle=1;fontSize=9;entryX=0;entryY=0.5;entryDx=0;entryDy=0;entryPerimeter=0;" edge="1" parent="1" source="ms2" target="db_mysql" />

        <!-- ARROWS: Telemetry Engine to Redis Cache -->
        <mxCell id="arr_ms_redis" value="In-Memory Cache" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#F59E0B;strokeWidth=2;fontColor=#B45309;fontStyle=1;fontSize=9;entryX=0;entryY=0.5;entryDx=0;entryDy=0;entryPerimeter=0;" edge="1" parent="1" source="ms3" target="db_redis" />

        <!-- ARROWS: Microservices to Enterprise Peer APIs -->
        <mxCell id="arr_ms_ext" value="RESTful API Sync" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;fontColor=#7F1D1D;fontStyle=1;fontSize=9;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="ms5" target="ext_payroll" />

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# Save to public/downloads and User's Downloads
pub_path = os.path.join(downloads_dir, "Figure3_Microservices_Diagram.drawio")
usr_path = os.path.join(user_downloads, "Figure3_Microservices_Diagram.drawio")

with open(pub_path, "w", encoding="utf-8") as f: f.write(fig3_detailed_xml)
with open(usr_path, "w", encoding="utf-8") as f: f.write(fig3_detailed_xml)

print("Successfully regenerated Figure3_Microservices_Diagram.drawio with full directional arrows!")
