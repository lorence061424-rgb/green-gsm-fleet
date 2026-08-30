# 🎨 Hirna Mobility Fleet & Transportation Management System
## Official UI/UX Design System & Style Guide (v2.0)

---

## 1. Design Philosophy & Aesthetic
The **Hirna Mobility Fleet & Transportation Management System** is designed as a modern, high-precision enterprise transportation command center. The visual identity bridges **Hirna’s iconic Philippine mobility heritage (Davao City & Metro Manila)** with modern software engineering principles:
* **High Contrast & Readability:** Built for high-stress dispatch environments and mobile field drivers under varying ambient sunlight.
* **Information Hierarchy:** Prioritizes critical live telemetry (GPS speed, safety alerts, dispatch states) with glanceable typography and color-coded status badges.
* **Responsive Command Layout:** Seamlessly scales across ultra-wide desktop dispatch consoles, tablet fleet management views, and driver mobile web screens.
* **Glassmorphic Accents & Clean Elevation:** Utilizes subtle backdrop blurs, soft card elevations, and rounded pill containers (`rounded-xl`, `shadow-sm`) to deliver a polished, enterprise-grade feel.

---

## 2. Brand Color Palette & Semantic Tokens

### 2.1 Primary Brand Identity Colors
| Token Name | Hex Code | Tailwind Equivalent | Role & Usage |
| :--- | :---: | :--- | :--- |
| **Hirna Crimson Red** | `#CE2029` | `bg-red-600` / `#CE2029` | **Primary Brand Color**; used for main CTAs, active navigation items, system headers, and primary branding. |
| **Deep Burgundy Dark** | `#7F1D1D` | `bg-red-900` | **Header & Accent Dark**; used for card borders, high-priority table headers, and command bar containers. |
| **Safety Gold / Amber**| `#F59E0B` | `bg-amber-500` | **Secondary Accent**; used for warning alerts, pending dispatches, EV charging indicators, and AI prediction badges. |
| **Eco-Emerald Green**  | `#10B981` | `bg-emerald-500` | **Success & Eco Metric**; used for high eco-safety scores (>90%), active GPS breadcrumbs, and completed dispatches. |

### 2.2 Neutral & Surface Background Tokens
| Token Name | Hex Code | Usage Context |
| :--- | :---: | :--- |
| **App Canvas Light** | `#F8FAFC` | Light mode primary body background (Slate-50). |
| **Card Surface White**| `#FFFFFF` | Primary container, widget, and modal surface. |
| **Subtle Border Tint** | `#E2E8F0` | Card borders, table gridlines, and divider rules (Slate-200). |
| **Charcoal Heading**  | `#0F172A` | H1, H2, H3 headings, and bold metric KPI numbers (Slate-900). |
| **Body Slate Text**   | `#334155` | Regular body paragraphs and form labels (Slate-700). |
| **Muted Metadata**    | `#64748B` | Secondary captions, timestamps, and helper text (Slate-500). |

---

## 3. Typography Hierarchy

The typography utilizes clean, modern geometric sans-serif fonts paired with tabular monospaced numerals for precise telematics rendering:

* **Primary Font Family:** `Inter`, `-apple-system`, `BlinkMacSystemFont`, `"Segoe UI"`, `Roboto`, `sans-serif`
* **Monospace Font (Telemetry & Code):** `JetBrains Mono`, `"Fira Code"`, `Consolas`, `monospace`

```css
/* Typography Scale Sample */
--font-display: 'Inter', sans-serif;
--font-mono: 'JetBrains Mono', monospace;

.metric-kpi-value {
  font-family: var(--font-display);
  font-size: 2.25rem; /* 36px */
  font-weight: 800;
  line-height: 1.1;
  color: #0F172A;
}

.telematics-coords {
  font-family: var(--font-mono);
  font-size: 0.8125rem; /* 13px */
  font-weight: 600;
  color: #475569;
}
```

---

## 4. Core UI Components & Patterns

### 4.1 Top Navigation & Command Bar
* **Position:** Fixed top with subtle glassmorphic backdrop blur (`backdrop-blur-md bg-white/90 border-b border-slate-200`).
* **Elements:**
  * **Brand Monogram:** Hirna Mobility Logo with active status pulse dot.
  * **Live Clock & Hub Indicator:** Real-time Philippine Standard Time (PST) sync + Active Hub Selector (Manila Port, BGC, NAIA, Davao Hubs).
  * **Quick Role Pill Badge:** Color-coded role pill (e.g., `Admin` in Crimson, `Finance` in Amber, `Driver` in Green).
  * **Alert Notification Bell:** Real-time badge counter streaming live speeding and harsh braking test alerts.

---

### 4.2 Interactive Leaflet.js GIS Map & Telemetry UI
* **Map Style:** CartoDB Positron / OpenStreetMap high-contrast clean tiles.
* **Vehicle Location Markers:**
  * Custom SVGs representing vehicle categories: 🚕 **Hirna Taxi** (Sedan), 🚐 **HiAce Van**, 🚙 **Innova MPV**, 🛺 **Hirna Traysikel**.
* **Pulsing Radar Aura Effect:**
  Active vehicle markers feature a glowing animated aura indicator:
  ```css
  @keyframes pulse-aura {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(206, 32, 41, 0.7);
    }
    70% {
      transform: scale(1.15);
      box-shadow: 0 0 0 12px rgba(206, 32, 41, 0);
    }
    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(206, 32, 41, 0);
    }
  }
  .marker-aura {
    animation: pulse-aura 2s infinite cubic-bezier(0.45, 0, 0.55, 1);
  }
  ```
* **Green Breadcrumb Route Trails:** Polyline layers rendered in `#10B981` (Emerald Green) with a stroke weight of `4px` and opacity of `0.85` indicating live vehicle paths.

---

### 4.3 KPI Metric Cards & Analytics Widgets
* **Card Elevation:** Rounded card containers (`rounded-2xl border border-slate-100 bg-white shadow-sm hover:shadow-md transition-all duration-200`).
* **Visual Structure:**
  1. Top Row: Metric Icon in a soft tinted circle + Metric Label in uppercase muted slate (`text-xs font-bold tracking-wider text-slate-500`).
  2. Middle Row: Large bold numeric value (`text-3xl font-extrabold text-slate-900`).
  3. Bottom Row: Trend badge (`+8.4% vs last week` in green / `-3.2% fuel burn` in amber) + secondary context helper.

---

### 4.4 Data Tables & Audit Logs
* **Header Style:** Clean slate background (`bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider py-3.5 px-4 border-b border-slate-200`).
* **Row Interactions:** Smooth hover state (`hover:bg-red-50/40 transition-colors`), alternating zebra tints, and responsive horizontal overflow handling.
* **Status Badges:**
  * `Dispatched` ➔ `bg-blue-50 text-blue-700 border border-blue-200`
  * `In-Transit` ➔ `bg-amber-50 text-amber-700 border border-amber-200`
  * `Completed` ➔ `bg-emerald-50 text-emerald-700 border border-emerald-200`
  * `Alert / Violation` ➔ `bg-red-50 text-red-700 border border-red-200`

---

### 4.5 Modals, Drawers & Forms
* **Form Inputs:** Floating labels, clear focus rings (`focus:ring-2 focus:ring-red-500 focus:border-red-500`), and real-time inline validation feedback.
* **Auto-Dispatch Toggle Switch:**
  * Pill switch with smooth sliding thumb (`bg-slate-300 peer-checked:bg-red-600 transition-colors`).
* **Export Action Buttons:**
  * **Primary Action:** Solid Crimson Red with white text (`bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm`).
  * **Secondary Action:** Ghost / Outlined slate button (`border border-slate-300 text-slate-700 hover:bg-slate-50`).

---

## 5. Mobile & Responsive Design Adaptations
| Viewport Size | Device Category | UI Adaptation Behavior |
| :--- | :--- | :--- |
| **`< 640px`** | Mobile Phones (Field Drivers) | Full-screen stack layout, bottom navigation dock, large touch-friendly buttons (`min-h-[48px]`), collapsible map viewport. |
| **`640px – 1024px`** | Tablets (Hub Supervisors) | 2-column KPI grid, collapsible sidebar drawer, split-screen map & dispatch list. |
| **`> 1024px`** | Desktop Dispatch Command | Full persistent sidebar, 4-column analytics dashboard, high-resolution Leaflet live map, real-time alert toast stream. |

---

## 6. Accessibility & Compliance (WCAG 2.1 AA)
* **Contrast Ratios:** All text elements maintain a minimum contrast ratio of `4.5:1` against background surfaces.
* **Color Independence:** Visual warnings (such as speeding alerts) are accompanied by distinct iconography (⚠️, 🛑, 🚗) and clear text labels, ensuring full readability for color-deficient users.
* **Keyboard Navigation:** Full tab-order focus styling (`outline-none focus-visible:ring-2 focus-visible:ring-red-500`) across all form inputs, modals, and data filters.
