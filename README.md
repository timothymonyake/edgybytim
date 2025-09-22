# EdgyByTim – Trading Journal & Discipline Tracker

**EdgyByTim** is a Laravel-powered trading journal built to help traders stay disciplined, track performance, and learn from past mistakes.  
Unlike traditional trade loggers, EdgyByTim focuses on the psychology and decision-making side of trading.

---

## Features

- **Trade Journal** – Record asset, session, RR, pips, direction, outcome, and more.  
- **Psychology Tracking** – Log emotions, narratives, and mistakes to improve mindset.  
- **Plan Adherence** – Track whether you followed your trading plan.  
- **Screenshots & Links** – Attach charts, daily logs, and external journal links.  
- **Expandable Details** – Keep tables clean while expanding rows for full context.  
- **Setup Classification** – Tag entries with setups (OB, FVG, IFVG, BB, etc.) using Select2 multi-selects.  
- **Performance Insights** – Aggregate totals for RR, pips, and session analysis.  
- **AJAX CRUD** – Smooth add, edit, and delete workflows without page reloads.  
- **Mistake Reminders** – A "Lessons Learned" section to help avoid repeating errors.  

---

## Roadmap

- Real-time dashboards with charts.  
- Weekly and monthly performance reports.  
- Integration with prop firm challenge tracking.  
- Configurable sticky notes and alerts for discipline reminders.  

---

## Tech Stack

- **Framework:** Laravel  
- **Frontend:** Blade + AdminLTE 3  
- **Database:** MySQL  
- **UI Enhancements:** Select2, AJAX CRUD  

---

## Getting Started

Clone the repository and install dependencies:

```bash
git clone https://github.com/yourusername/edgybytim.git
cd edgybytim
composer install
npm install && npm run dev
php artisan migrate
php artisan serve
