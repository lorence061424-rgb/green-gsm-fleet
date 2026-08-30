import os

downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
user_downloads = os.path.expanduser(r"~\Downloads")
os.makedirs(downloads_dir, exist_ok=True)

# High-precision Draw.io XML for Figure 9: Complete Enterprise Integration Diagram (All 9 Enterprise Teams)
fig9_detailed_xml = """<mxfile host="Electron" modified="2026-08-31T00:03:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure9_Detailed" name="Figure 9 - Enterprise Integration Diagram">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        
        <!-- Main Title Banner -->
        <mxCell id="title" value="FIGURE 9: ENTERPRISE SYSTEM INTEGRATION DIAGRAM (TEAMS 1 - 10 &amp; HR 1 - 4)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=14;" vertex="1" parent="1">
          <mxGeometry x="80" y="20" width="1000" height="40" as="geometry" />
        </mxCell>

        <!-- Core System: Team 7 Fleet & Transportation System -->
        <mxCell id="core_sys" value="TEAM 7: HIRNA FLEET &amp; TRANSPORTATION&#10;MANAGEMENT SYSTEM (CORE PLATFORM)&#10;&#10;• Fleet Vehicle Management (FVM)&#10;• Vehicle Roster &amp; Dispatch (VRDS)&#10;• Dynamic Telematics &amp; Predictive Maintenance (DTPM)&#10;• Fuel Monitoring &amp; AI Energy Predictor (FMS)&#10;• Transport Cost Analysis (TCAO)&#10;• Route Planning &amp; Optimization (RPO)&#10;• Mobile Command Application" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=11;align=left;spacingLeft=15;" vertex="1" parent="1">
          <mxGeometry x="40" y="90" width="300" height="490" as="geometry" />
        </mxCell>

        <!-- API Middleware & Gateway -->
        <mxCell id="gw_sys" value="ENTERPRISE RESTful API GATEWAY&#10;(/api/v1/* JSON Endpoints + Bearer Auth)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=11;rotation=-90;" vertex="1" parent="1">
          <mxGeometry x="250" y="295" width="260" height="80" as="geometry" />
        </mxCell>

        <!-- 9 Peer Enterprise Systems (Right Side) -->

        <!-- 1. HR 1 (Team 1) -->
        <mxCell id="t1" value="Team 1: HR Recruitment &amp; Onboarding (HR 1)&#10;Endpoint: /api/v1/hr1/recruitment&#10;Payload: Driver background clearance &amp; license verification" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="80" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 2. HR 2 (Team 2) -->
        <mxCell id="t2" value="Team 2: HR Workforce Management (HR 2)&#10;Endpoint: /api/v1/hr2/workforce&#10;Payload: Driver shift schedules &amp; duty timesheets" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="135" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 3. HR 3 (Team 3) -->
        <mxCell id="t3" value="Team 3: HR Performance &amp; Development (HR 3)&#10;Endpoint: /api/v1/hr3/performance&#10;Payload: Driver eco-safety ratings &amp; incident logs" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="190" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 4. HR 4 (Team 4) -->
        <mxCell id="t4" value="Team 4: HR Payroll &amp; Benefits (HR 4)&#10;Endpoint: /api/v1/hr4/payroll&#10;Payload: Driver safety bonus records &amp; penalty deductions" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="245" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 5. Team 5: Financial Management System -->
        <mxCell id="t5" value="Team 5: Financial Management System (FMS)&#10;Endpoint: /api/v1/finance/ledger&#10;Payload: Cost-Per-Kilometer (CPK) sync &amp; fuel AP ledger" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#10B981;fontColor=#047857;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="300" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 6. Team 6: Supply Chain & Inventory -->
        <mxCell id="t6" value="Team 6: Supply Chain &amp; Inventory (SWS/DTRS)&#10;Endpoint: /api/v1/inventory/warehouse&#10;Payload: Fuel stock inventory (Gas/Diesel/EV) &amp; parts log" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#F59E0B;fontColor=#B45309;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="355" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 7. Team 8: Facilities & Admin -->
        <mxCell id="t8" value="Team 8: Facilities &amp; Administrative Management&#10;Endpoint: /api/v1/admin/legal&#10;Payload: Vehicle LTFRB franchise contracts &amp; legal status" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="410" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 8. Team 9: TNVS Operations -->
        <mxCell id="t9" value="Team 9: TNVS Operations &amp; Driver Management&#10;Endpoint: /api/v1/operations/dispatch&#10;Payload: Real-time dispatch status, pairings &amp; wallet balance" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="465" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- 9. Team 10: TNVS Booking & CX -->
        <mxCell id="t10" value="Team 10: TNVS Booking, Payments &amp; CX&#10;Endpoint: /api/v1/booking/tracking&#10;Payload: Passenger booking reference, Leaflet GPS ETA feed" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;fontColor=#7F1D1D;fontSize=10;fontStyle=1;align=left;spacingLeft=10;" vertex="1" parent="1">
          <mxGeometry x="520" y="520" width="370" height="45" as="geometry" />
        </mxCell>

        <!-- Arrows: Core System to API Gateway -->
        <mxCell id="a_core_gw" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=3;" edge="1" parent="1" source="core_sys" target="gw_sys" />

        <!-- Arrows: API Gateway to each of the 9 Peer Systems -->
        <mxCell id="a1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t1" />
        <mxCell id="a2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t2" />
        <mxCell id="a3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t3" />
        <mxCell id="a4" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t4" />
        <mxCell id="a5" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#10B981;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t5" />
        <mxCell id="a6" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#F59E0B;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t6" />
        <mxCell id="a7" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t8" />
        <mxCell id="a8" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t9" />
        <mxCell id="a9" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=2;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="gw_sys" target="t10" />

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# Save to public/downloads and User's Downloads
pub_path = os.path.join(downloads_dir, "Figure9_Integration_Diagram.drawio")
usr_path = os.path.join(user_downloads, "Figure9_Integration_Diagram.drawio")

with open(pub_path, "w", encoding="utf-8") as f: f.write(fig9_detailed_xml)
with open(usr_path, "w", encoding="utf-8") as f: f.write(fig9_detailed_xml)

print("Successfully regenerated Figure9_Integration_Diagram.drawio with all 9 enterprise teams!")
