<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/partialViews/schedule.css">
</head>
<section class="section">
    <div class="container">
        <div class="header">
            <h2 class="title">Tour Schedule</h2>    
        </div>

        <div class="filters">
            <div class="filter-group">
                <button class="filter-btn active" data-filter="all">All Days</button>
                <button class="filter-btn" data-filter="day1">Thursday</button>
                <button class="filter-btn" data-filter="day2">Friday</button>
                <button class="filter-btn" data-filter="day3">Saturday</button>
                <button class="filter-btn" data-filter="day4">Sunday</button>
            </div>
            <div class="filter-group">
                <button class="filter-btn active" data-filter="all">All Languages</button>
                <button class="filter-btn" data-filter="nl">Dutch</button>
                <button class="filter-btn" data-filter="en">English</button>
                <button class="filter-btn" data-filter="ch">Chinese</button>
            </div>
        </div>
        
        <div class="schedule-list">
            <div class="day-group">
                <h3 class="day-title">Thursday - July 26, 2026</h3>
                
                <div class="schedule-row">
                    <div class="col-date">July 26, 2026</div>
                    <div class="col-time">10:00 - 12:30</div>
                    <div class="col-title">A Stroll Through History</div>
                    <div class="col-lang"><span class="badge-lang">EN</span></div>
                    <div class="col-status"><span class="status-available">8 spots left</span></div>
                    <div class="col-price">€37,50</div>
                    <div class="col-action"><button class="btn-book">Book Now</button></div>
                </div>
        </div>
    </div>
</section>