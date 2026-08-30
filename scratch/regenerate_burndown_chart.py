import os

downloads_dir = r"c:\xamppp\htdocs\TNVS\public\downloads"
user_downloads = os.path.expanduser(r"~\Downloads")
os.makedirs(downloads_dir, exist_ok=True)

# High-precision Draw.io XML for Figure 2: Burndown Chart
fig2_detailed_xml = """<mxfile host="Electron" modified="2026-08-30T23:23:00.000Z" agent="Mozilla/5.0" version="21.6.8" type="device">
  <diagram id="Figure2_Detailed" name="Figure 2 - Sprint Burndown Chart">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1169" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        
        <!-- Header Banner -->
        <mxCell id="hdr" value="FIGURE 2: SPRINT BURNDOWN CHART &amp; TEAM VELOCITY (SPRINTS 1 - 6)" style="text;html=1;strokeColor=none;fillColor=#CE2029;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=1;fontColor=#FFFFFF;fontStyle=1;fontSize=15;" vertex="1" parent="1">
          <mxGeometry x="120" y="30" width="920" height="40" as="geometry" />
        </mxCell>
        
        <!-- Chart Canvas -->
        <mxCell id="canvas" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFF8F8;strokeColor=#CE2029;strokeWidth=2;" vertex="1" parent="1">
          <mxGeometry x="120" y="90" width="920" height="420" as="geometry" />
        </mxCell>
        
        <!-- Y-Axis Title -->
        <mxCell id="y_title" value="REMAINING STORY POINTS" style="text;html=1;align=center;verticalAlign=middle;fontColor=#7F1D1D;fontStyle=1;fontSize=12;rotation=-90;" vertex="1" parent="1">
          <mxGeometry x="60" y="270" width="160" height="30" as="geometry" />
        </mxCell>
        
        <!-- X-Axis Title -->
        <mxCell id="x_title" value="AGILE SPRINT CYCLES (2 WEEKS PER SPRINT)" style="text;html=1;align=center;verticalAlign=middle;fontColor=#7F1D1D;fontStyle=1;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="400" y="470" width="360" height="30" as="geometry" />
        </mxCell>
        
        <!-- Grid Guidelines (Y-Axis Labels) -->
        <mxCell id="y100" value="100 pts" style="text;html=1;align=right;verticalAlign=middle;fontSize=10;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="140" y="130" width="50" height="20" as="geometry" />
        </mxCell>
        <mxCell id="y80" value="80 pts" style="text;html=1;align=right;verticalAlign=middle;fontSize=10;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="140" y="190" width="50" height="20" as="geometry" />
        </mxCell>
        <mxCell id="y60" value="60 pts" style="text;html=1;align=right;verticalAlign=middle;fontSize=10;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="140" y="250" width="50" height="20" as="geometry" />
        </mxCell>
        <mxCell id="y40" value="40 pts" style="text;html=1;align=right;verticalAlign=middle;fontSize=10;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="140" y="310" width="50" height="20" as="geometry" />
        </mxCell>
        <mxCell id="y20" value="20 pts" style="text;html=1;align=right;verticalAlign=middle;fontSize=10;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="140" y="370" width="50" height="20" as="geometry" />
        </mxCell>
        <mxCell id="y0" value="0 pts" style="text;html=1;align=right;verticalAlign=middle;fontSize=10;fontColor=#666666;" vertex="1" parent="1">
          <mxGeometry x="140" y="430" width="50" height="20" as="geometry" />
        </mxCell>

        <!-- Ideal Guideline (Dashed Grey Line) -->
        <mxCell id="pt_ideal_start" value="" style="ellipse;whiteSpace=wrap;html=1;fillColor=#9CA3AF;strokeColor=none;" vertex="1" parent="1">
          <mxGeometry x="205" y="138" width="6" height="6" as="geometry" />
        </mxCell>
        <mxCell id="pt_ideal_end" value="" style="ellipse;whiteSpace=wrap;html=1;fillColor=#9CA3AF;strokeColor=none;" vertex="1" parent="1">
          <mxGeometry x="965" y="438" width="6" height="6" as="geometry" />
        </mxCell>
        <mxCell id="ideal_line" value="Ideal Guideline Line" style="endArrow=none;dashed=1;html=1;strokeColor=#9CA3AF;strokeWidth=2;fontSize=10;fontColor=#6B7280;" edge="1" parent="1" source="pt_ideal_start" target="pt_ideal_end" />

        <!-- Actual Burndown Data Points (Hirna Team) -->
        <mxCell id="p0" value="Start&#10;100 pts" style="ellipse;whiteSpace=wrap;html=1;fillColor=#7F1D1D;fontColor=#FFFFFF;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="180" y="115" width="60" height="50" as="geometry" />
        </mxCell>
        
        <mxCell id="p1" value="Sprint 1&#10;85 pts" style="ellipse;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="320" y="160" width="65" height="50" as="geometry" />
        </mxCell>

        <mxCell id="p2" value="Sprint 2&#10;68 pts" style="ellipse;whiteSpace=wrap;html=1;fillColor=#CE2029;fontColor=#FFFFFF;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="460" y="210" width="65" height="50" as="geometry" />
        </mxCell>

        <mxCell id="p3" value="Sprint 3&#10;48 pts" style="ellipse;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="600" y="270" width="65" height="50" as="geometry" />
        </mxCell>

        <mxCell id="p4" value="Sprint 4&#10;28 pts" style="ellipse;whiteSpace=wrap;html=1;fillColor=#F59E0B;fontColor=#000000;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="740" y="330" width="65" height="50" as="geometry" />
        </mxCell>

        <mxCell id="p5" value="Sprint 5&#10;12 pts" style="ellipse;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="860" y="380" width="65" height="50" as="geometry" />
        </mxCell>

        <mxCell id="p6" value="Sprint 6&#10;0 pts (Shipped)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#10B981;fontColor=#FFFFFF;fontStyle=1;fontSize=10;align=center;" vertex="1" parent="1">
          <mxGeometry x="940" y="415" width="85" height="50" as="geometry" />
        </mxCell>

        <!-- Burndown Connectors (Solid Crimson/Green Line) -->
        <mxCell id="e0" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=3;" edge="1" parent="1" source="p0" target="p1" />
        <mxCell id="e1" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#CE2029;strokeWidth=3;" edge="1" parent="1" source="p1" target="p2" />
        <mxCell id="e2" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#F59E0B;strokeWidth=3;" edge="1" parent="1" source="p2" target="p3" />
        <mxCell id="e3" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#F59E0B;strokeWidth=3;" edge="1" parent="1" source="p3" target="p4" />
        <mxCell id="e4" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#10B981;strokeWidth=3;" edge="1" parent="1" source="p4" target="p5" />
        <mxCell id="e5" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#10B981;strokeWidth=3;" edge="1" parent="1" source="p5" target="p6" />

        <!-- Legend Box -->
        <mxCell id="lgnd" value="BURNDOWN LEGEND:&#10;━━ Actual Team Burndown (Sprints 1-6)&#10;···· Ideal Burn Guideline" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#FFFFFF;strokeColor=#CE2029;fontSize=10;fontStyle=1;fontColor=#7F1D1D;" vertex="1" parent="1">
          <mxGeometry x="720" y="110" width="280" height="50" as="geometry" />
        </mxCell>

      </root>
    </mxGraphModel>
  </diagram>
</mxfile>"""

# Save to public/downloads and User's Downloads
pub_path = os.path.join(downloads_dir, "Figure2_Burndown_Chart.drawio")
usr_path = os.path.join(user_downloads, "Figure2_Burndown_Chart.drawio")

with open(pub_path, "w", encoding="utf-8") as f: f.write(fig2_detailed_xml)
with open(usr_path, "w", encoding="utf-8") as f: f.write(fig2_detailed_xml)

print("Successfully regenerated Figure2_Burndown_Chart.drawio!")
