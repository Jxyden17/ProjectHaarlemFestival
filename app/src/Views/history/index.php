<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Haarlem Festival</title>
    <link href="/css/History/index.css" rel="stylesheet">
</head>
<body>

    <?php require __DIR__ . '/../partialsViews/hero.php'; ?>
    <?php require __DIR__ . '/../partialsViews/stops-grid.php'; ?> 

    <section class="discover-section">
        <div class="discover-container">
            <h1 class="discover-title">Discover Historic Haarlem</h1>
            <p class="discover-description">
                Discover why Haarlem is called the 'Little Amsterdam'—but with more charm and fewer crowds. In this exclusive 2.5-hour 
                guided walking tour, you will travel back to the Dutch Golden Age. <br/><br/>
                From the bustling Grote Markt to the hidden hofjes (courtyards) where time seems to stand still, Our expert guides will 
                reveal the stories behind the facades, the secrets of the spice trade, and the legends of local heroes like Kenau.
            </p>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">⏱️</div>
                    <div class="info-label">Duration</div>
                    <div class="info-value">2.5 hours</div>
                </div>
                <div class="info-item">
                    <div class="info-icon">👥</div>
                    <div class="info-label">Group size</div>
                    <div class="info-value">Max 12 participants</div>
                </div>
                <div class="info-item">
                    <div class="info-icon">🗣️</div>
                    <div class="info-label">Language</div>
                    <div class="info-value">NL/EN</div>
                </div>
                <div class="info-item">
                    <div class="info-icon">⭐</div>
                    <div class="info-label">Reviews</div>
                    <div class="info-value">4.9 (127 reviews)</div>
                </div>
            </div>

            <div class="pricing-info-row">
                <div class="pricing-section">
                    <h2 class="section-title">Prices</h2>
                    <div class="price-item">
                        <div class="price-label">
                            <span class="price-name">Regular Ticket</span>
                            <span class="price-subtitle">Per person</span>
                        </div>
                        <div class="price-value">€37,50</div>
                    </div>
                    <div class="price-item">
                        <div class="price-label">
                            <span class="price-name">Family Ticket</span>
                            <span class="price-subtitle">2 adults + 2 kids</span>
                        </div>
                        <div class="price-value">€60,00</div>
                    </div>
                </div>
                <div class="info-section">
                    <h2 class="section-title">Important Information</h2>
                    <ul class="info-list">
                        <li>Minimum age: 12 years</li>
                        <li>Strollers are not allowed</li>
                        <li>Group size: 12 participants + 1 guide</li>
                        <li>Breaks at cáfeterías (stop 5)</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/../partialsViews/schedule.php'; ?> 

    <section class="bottom-content-section">
    <div class="route-guides-row">
        
        <div class="route-container">
            <h2 class="section-title-alt">The Route</h2>
            <p class="section-subtitle">A Historical Walkpath</p>
            <div class="map-wrapper">
                <img src="/img/historyIMG/hero.png" alt="Tour Route Map">
            </div>
        </div>

        <div class="guides-container">
            <h2 class="section-title-alt">Meet Your Guide</h2>
            <p class="guide-intro">
                Our guides are local historians who love their city. Find out who leads you
                through old exhibitions, hidden courtyards, and the legends of Haarlem.
            </p>
            
            <div class="guides-grid">
                <div class="guide-card">
                    <img src="/img/historyIMG/hero.png" alt="Guide 1" class="guide-img">
                    <h4>Albert</h4>
                    <p class="guide-role">Historian</p>
                    <p class="guide-desc">Expert on the Dutch Golden Age and Haarlem's secrets.</p>
                </div>
                <div class="guide-card">
                    <img src="/img/historyIMG/hero.png" alt="Guide 2" class="guide-img">
                    <h4>Marta</h4>
                    <p class="guide-role">Local Guide</p>
                    <p class="guide-desc">Passionate about hidden courtyards and local art.</p>
                </div>
                <div class="guide-card">
                    <img src="/img/historyIMG/hero.png" alt="Guide 3" class="guide-img">
                    <h4>Pim</h4>
                    <p class="guide-role">Architect</p>
                    <p class="guide-desc">Tells the stories behind the facades and monuments.</p>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>