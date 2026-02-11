<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stories in Haarlem Festival</title>
    <link href="/css/Stories/styles.css" rel="stylesheet">
</head>
<body>
<section class="hero" aria-labelledby="hero-title">
  <div class="hero__inner container">
    <p class="hero__badge">
      <span class="hero__badge-icon" aria-hidden="true">📅</span>
      <span>This Weekend • July 24–27, 2025</span>
    </p>
    <h1 id="hero-title" class="hero__title">Stories in Haarlem</h1>
    <p class="hero__lead">
      Experience the magic of storytelling in Haarlem. From intimate spoken-word performances to moving
      narratives inspired by the city’s history, Stories in Haarlem invites you to listen, imagine, and connect.
    </p>

    <p class="hero__sub">
      A cultural festival celebrating storytelling, imagination, and human experience.
    </p>

    <div class="hero__meta" aria-label="Festival statistics">
      <div class="stat-card">
        <span class="stat-card__icon" aria-hidden="true">🗓️</span>
        <div class="stat-card__text">
          <div class="stat-card__label">Total Events</div>
          <div class="stat-card__value">15 <span class="stat-card__unit">Shows</span></div>
        </div>
      </div>

      <div class="stat-card">
        <span class="stat-card__icon" aria-hidden="true">📍</span>
        <div class="stat-card__text">
          <div class="stat-card__label">Venues</div>
          <div class="stat-card__value">6 <span class="stat-card__unit">Locations</span></div>
        </div>
      </div>
    </div>

    <div class="hero__actions">
      <a class="btn btn--primary" href="/program" aria-label="View the full program">
        View program
        <span class="btn__icon" aria-hidden="true">↓</span>
      </a>
    </div>
  </div>
</section>


<section class="feature container" aria-labelledby="feature-title">
  <div class="feature-card">
    <div 
      class="feature-card__media"
      role="img"
      aria-label="Evening storytelling event with audience in Haarlem"
      >
    </div>
=
    <div class="feature-card__overlay">
      <h2 id="feature-title" class="feature-card__title">
        Live stories across the city
      </h2>
      <p class="feature-card__subtitle">
        Intimate stories, live podcasts, and family theater
      </p>
    </div>
  </div>

  <div class="feature__text">
    <p>
      Stories in Haarlem brings together a diverse program of storytelling formats, including spoken-word
      performances, live podcasts, and theatrical stories for all ages. The festival explores themes of
      history, imagination, and human experience, with stories inspired by both local heritage and
      contemporary perspectives.
    </p>

    <p>
      Events take place at various locations across Haarlem, creating an accessible and intimate atmosphere
      where audiences can listen, reflect, and connect through stories.
    </p>
  </div>
</section>
<section class="storytellers container" aria-labelledby="storytellers-title">
  <header class="storytellers__header">
    <h2 id="storytellers-title" class="storytellers__title">Featured Storytellers</h2>
  </header>

  <div class="storytellers__grid">
    <!-- Card 1 -->
    <article class="story-card" aria-labelledby="st-1-title">
      <div class="story-card__media">
        <img
          src="/img/storytellers/mister-anansi.jpg"
          alt="Outdoor storytelling performance for children in a green park"
          loading="lazy"
        />
      </div>

      <div class="story-card__body">
        <span class="story-card__tag">Kids</span>
        <h3 id="st-1-title" class="story-card__title">Mister Anansi</h3>
        <p class="story-card__text">
          Caribbean spider tales brought to life with vibrant puppetry and storytelling
        </p>
        <a class="story-card__btn" href="/stories/mister-anansi" aria-label="View profile: Mister Anansi">
          View Profile
        </a>
      </div>
    </article>

    <!-- Card 2 -->
    <article class="story-card" aria-labelledby="st-2-title">
      <div class="story-card__media">
        <img
          src="/img/storytellers/omdenken-podcast.jpg"
          alt="Podcast recording on a small stage with an audience"
          loading="lazy"
        />
      </div>

      <div class="story-card__body">
        <span class="story-card__tag">Adults</span>
        <h3 id="st-2-title" class="story-card__title">Omdenken Podcast</h3>
        <p class="story-card__text">
          Live podcast recording exploring perspectives and challenging assumptions
        </p>

        <a class="story-card__btn" href="/stories/omdenken-podcast" aria-label="View profile: Omdenken Podcast">
          View Profile
        </a>
      </div>
    </article>

    <!-- Card 3 -->
    <article class="story-card" aria-labelledby="st-3-title">
      <div class="story-card__media">
        <img
          src="/img/storytellers/corrie-ten-boom.jpg"
          alt="Historic living room interior associated with Corrie ten Boom"
          loading="lazy"
        />
      </div>

      <div class="story-card__body">
        <span class="story-card__tag">History</span>
        <h3 id="st-3-title" class="story-card__title">Corrie ten Boom</h3>
        <p class="story-card__text">
          The powerful story of a Dutch family who saved Jewish lives during WWII
        </p>

        <a class="story-card__btn" href="/stories/corrie-ten-boom" aria-label="View profile: Corrie ten Boom">
          View Profile
        </a>
      </div>
    </article>
  </div>
</section>

  <?php require __DIR__ . '/../partialsViews/schedule.php'; ?> 

<section class="venues container" aria-labelledby="venues-title">
  <header class="venues__header">
    <h2 id="venues-title" class="venues__title">Festival Venues</h2>
    <p class="venues__subtitle">
      Plan your storytelling journey through the festival
    </p>
  </header>

  <div class="venues__grid">
    <!-- Venue 1 -->
    <article class="venue-card">
      <h3 class="venue-card__title">
        <span class="venue-card__icon" aria-hidden="true">📍</span>
        Verhalenhuis Haarlem
      </h3>
      <p class="venue-card__address">Van Egmondstraat 7, Haarlem-Noord</p>

      <p class="venue-card__text">
        A cultural storytelling venue dedicated to spoken word, family stories, and community storytelling.
        Verhalenhuis Haarlem hosts performances for all ages, including children’s stories and storytelling competitions.
      </p>

      <div class="venue-card__tags">
        <span class="tag">Stories for the whole family</span>
        <span class="tag">Cultural venue</span>
        <span class="tag">Community storytelling</span>
      </div>
    </article>

    <!-- Venue 2 -->
    <article class="venue-card">
      <h3 class="venue-card__title">
        <span class="venue-card__icon" aria-hidden="true">📍</span>
        Haarlemmerhout
      </h3>
      <p class="venue-card__address">Haarlemmerhout, 2012 ED Haarlem</p>

      <p class="venue-card__text">
        The oldest public park in the Netherlands, used as an outdoor location for storytelling events.
        Performances take place in a natural setting, creating an intimate atmosphere under the trees.
      </p>

      <div class="venue-card__tags">
        <span class="tag">Outdoor seating</span>
        <span class="tag">Weather dependent</span>
        <span class="tag">Picnic area</span>
      </div>
    </article>

    <!-- Venue 3 -->
    <article class="venue-card">
      <h3 class="venue-card__title">
        <span class="venue-card__icon" aria-hidden="true">📍</span>
        Buurderij Haarlem (Kweekcafé)
      </h3>
      <p class="venue-card__address">Zijlweg 184, 2015 BH Haarlem</p>

      <p class="venue-card__text">
        A neighborhood café and community space hosting storytelling with social and historical impact.
        The venue offers an informal setting for intimate stories and audience-focused performances.
      </p>

      <div class="venue-card__tags">
        <span class="tag">Wheelchair accessible</span>
        <span class="tag">Parking available</span>
        <span class="tag">Restrooms</span>
      </div>
    </article>

    <!-- Venue 4 -->
    <article class="venue-card">
      <h3 class="venue-card__title">
        <span class="venue-card__icon" aria-hidden="true">📍</span>
        Corrie ten Boom Huis
      </h3>
      <p class="venue-card__address">Barteljorisstraat 19, 2011 RA Haarlem</p>

      <p class="venue-card__text">
        The historic home of the Ten Boom family, used as a location for impactful storytelling
        about history and World War II. The venue adds authenticity and depth to historical performances.
      </p>

      <div class="venue-card__tags">
        <span class="tag">Historical museum</span>
        <span class="tag">Guided tours</span>
        <span class="tag">Restrooms</span>
      </div>
    </article>

    <!-- Venue 5 -->
    <article class="venue-card">
      <h3 class="venue-card__title">
        <span class="venue-card__icon" aria-hidden="true">📍</span>
        De Schuur
      </h3>
      <p class="venue-card__address">Lange Begijnestraat 9, 2011 HH Haarlem</p>

      <p class="venue-card__text">
        A well-known cultural venue hosting podcasts and live recordings with an audience.
        De Schuur is mainly used for adult-oriented storytelling and discussion-based performances.
      </p>

      <div class="venue-card__tags">
        <span class="tag">Adult audience</span>
        <span class="tag">Cultural venue</span>
        <span class="tag">Live podcast recording</span>
      </div>
    </article>

    <!-- Venue 6 -->
    <article class="venue-card">
      <h3 class="venue-card__title">
        <span class="venue-card__icon" aria-hidden="true">📍</span>
        Theater Elswout
      </h3>
      <p class="venue-card__address">Elswoutslaan 24-A, 2051 AE Overveen</p>

      <p class="venue-card__text">
        A theater venue located near nature, hosting storytelling performances for children,
        families, and mixed-age audiences. The theater provides a calm and accessible setting
        for longer performances.
      </p>

      <div class="venue-card__tags">
        <span class="tag">Stories for the whole family</span>
        <span class="tag">Family-friendly</span>
        <span class="tag">Theater venue</span>
      </div>
    </article>
  </div>
</section>

<section class="extras container" aria-labelledby="extras-title">
  <header class="extras__header">
    <h2 id="extras-title" class="extras__title">Explore more during The Festival</h2>
  </header>

  <div class="extras__grid">
    <article class="extra-card" aria-labelledby="extra-1-title">
      <div class="extra-card__top">
        <span class="extra-card__icon" aria-hidden="true">🍬</span>
        <h3 id="extra-1-title" class="extra-card__title">Yummy!</h3>
      </div>

      <p class="extra-card__text">
        Pair your storytelling experience with a curated dinner at one of Haarlem’s restaurants.
      </p>

      <a class="extra-card__btn" href="/yummy" aria-label="View Yummy">
        View Yummy!
      </a>
    </article>

    <article class="extra-card" aria-labelledby="extra-2-title">
      <div class="extra-card__top">
        <span class="extra-card__icon" aria-hidden="true">🎷</span>
        <h3 id="extra-2-title" class="extra-card__title">Haarlem Jazz</h3>
      </div>

      <p class="extra-card__text">
        Enjoy live jazz performances across the city during the festival evenings.
      </p>

      <a class="extra-card__btn" href="/jazz" aria-label="View Haarlem Jazz">
        View Haarlem Jazz
      </a>
    </article>
  </div>
</section>

<section class="faq container" aria-labelledby="faq-title">
  <header class="faq__header">
    <h2 id="faq-title" class="faq__title">FAQ</h2>
  </header>

  <div class="faq__list">
    <details class="faq-item">
      <summary class="faq-item__summary">Are stories available in English?</summary>
      <div class="faq-item__content">
        <p>
          Yes. Several sessions are offered in English, and some performances include bilingual elements.
          Check each event page for language information.
        </p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-item__summary">Is the festival suitable for children?</summary>
      <div class="faq-item__content">
        <p>
          Absolutely. We have kid-friendly shows and family-oriented stories. Look for “Kids” or “Family-friendly”
          labels in the program.
        </p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-item__summary">What does “pay as you like” mean?</summary>
      <div class="faq-item__content">
        <p>
          It means you choose how much to pay based on what you can afford or what you feel the experience was worth.
          Some events may have a recommended contribution.
        </p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-item__summary">Do I need to reserve tickets in advance?</summary>
      <div class="faq-item__content">
        <p>
          For popular shows, yes. Some venues have limited seating. If a reservation is required, it will be shown on
          the event page.
        </p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-item__summary">Where do the performances take place?</summary>
      <div class="faq-item__content">
        <p>
          Performances take place at multiple venues across Haarlem, including cultural venues, parks, and theaters.
          See the “Festival Venues” section for details.
        </p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-item__summary">Are there any discounts available?</summary>
      <div class="faq-item__content">
        <p>
          Some events offer discounted tickets for students or groups. If available, you’ll see it during checkout or
          on the event page.
        </p>
      </div>
    </details>
  </div>
</section>

</body>