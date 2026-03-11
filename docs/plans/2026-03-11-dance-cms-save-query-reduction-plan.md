# Dance CMS Save Query Reduction Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Reduce unnecessary database queries during dance CMS home/detail saves without changing CMS behavior.

**Architecture:** Keep the current service and repository boundaries, but reduce duplicate reads and repeated write lookups inside the dance CMS save flow. Focus on reusing already loaded page data, batching page section lookup work, and avoiding unnecessary item existence checks where safe.

**Tech Stack:** PHP, PDO, custom MVC/service/repository architecture

---

### Task 1: Document Current Query Paths

**Files:**
- Modify: `app/src/Service/Cms/CmsDanceService.php`
- Modify: `app/src/Service/Cms/CmsPageSaveService.php`
- Modify: `app/src/Repository/PageRepository.php`
- Modify: `app/src/Repository/DanceRepository.php`

**Step 1: Add short temporary query-path notes in comments or working notes**

Document the current save paths:
- dance home save
- dance detail save
- page section update path
- section item update path

**Step 2: Confirm the current hot spots**

Verify these are still true before changing behavior:
- detail save loads detail meta and existing page content
- page save resolves page id by slug
- page save loads section ids once
- section item updates still do one `UPDATE` per item and optional fallback existence `SELECT`

**Step 3: Remove temporary comments if they reduce readability**

Keep only useful comments if a flow is genuinely non-obvious.

---

### Task 2: Reuse Existing Page Data More Aggressively In Dance Detail Save

**Files:**
- Modify: `app/src/Service/Cms/CmsDanceService.php`
- Modify: `app/src/Service/Cms/CmsPageSaveService.php`

**Step 1: Identify whether detail save can pass page id directly**

Check whether the already loaded page object for dance detail save can carry enough identity to avoid a later slug-to-id lookup.

**Step 2: Add a `savePageContentById(...)` or equivalent direct path if needed**

If the current save service only supports slug-based save, add a direct-id save path so callers that already know the page id do not re-query it.

Example target shape:

```php
public function savePageContent(int $pageId, ?string $pageTitle, array $sections): void
{
    // existing implementation
}
```

Use the direct method from dance detail save if page id is already known or can be obtained from already loaded rows.

**Step 3: Update dance detail save to reuse already available identity data**

Avoid this pattern if possible:
- load page by slug
- later resolve page id by same slug again

Prefer:
- load page once
- pass resolved page identity into save directly

**Step 4: Manually verify dance detail save still preserves existing track audio URLs**

Check:
- unchanged track audio URLs remain intact
- updated tracks still save correctly

---

### Task 3: Reduce Repeated Section Item Existence Checks

**Files:**
- Modify: `app/src/Repository/PageRepository.php`

**Step 1: Review the current `saveOrUpdateSectionItems()` behavior**

Current flow:
- run `UPDATE`
- if zero rows affected, run `SELECT 1`
- throw if not found

This is defensive, but can add extra queries during normal saves.

**Step 2: Decide on the safer low-risk optimization**

Recommended option:
- preload allowed item ids for the section once
- validate requested ids against that set
- then run updates without a per-item fallback `SELECT`

Example helper:

```php
private function findSectionItemIds(int $sectionId): array
{
    // SELECT id FROM section_items WHERE section_id = :section_id
}
```

**Step 3: Update `saveOrUpdateSectionItems()`**

New flow:
- load all existing item ids for the section once
- validate each incoming item id against the map
- perform `UPDATE` directly

This changes:
- from up to `N` fallback `SELECT`s
- to `1` upfront lookup for the section

**Step 4: Keep current error behavior**

If an item id does not exist for the section, still throw a runtime exception with a clear message.

---

### Task 4: Reduce Duplicate Section-Item Save Work For Empty Sections

**Files:**
- Modify: `app/src/Service/Cms/CmsPageSaveService.php`
- Modify: `app/src/Mapper/CmsDanceMapper.php`

**Step 1: Review which dance sections always have empty item arrays**

Examples:
- `dance_schedule`
- `dance_banner`
- `dance_info`
- `dance_capacity`
- `dance_special_session`
- `dance_detail_info`

**Step 2: Ensure these sections never trigger unnecessary item persistence work**

Keep item arrays empty and do not run item-save logic for those sections.

**Step 3: Confirm mapper output stays minimal**

The mapper should only include item arrays where item persistence is actually required:
- passes
- hero images
- highlights
- tracks

---

### Task 5: Optional Follow-Up: Add Page Identity To Page Model Or Save Contract

**Files:**
- Modify: `app/src/Models/Page/Page.php`
- Modify: `app/src/Mapper/PageMapper.php`
- Modify: `app/src/Service/PageService.php`
- Modify: `app/src/Service/Cms/CmsDanceService.php`
- Modify: `app/src/Service/Cms/CmsPageSaveService.php`

**Step 1: Decide whether `Page` should carry database id**

If the project benefits from a stronger page identity model, add `id` to `Page`.

**Step 2: Map page id from repository rows into the model**

Then CMS save callers that already loaded a page can pass its id directly, avoiding slug re-resolution.

**Step 3: Only do this if the added model coupling is worth it**

This is optional because it affects more files and slightly changes the domain model.

---

### Task 6: Manual Verification

**Files:**
- Verify: `app/src/Service/Cms/CmsDanceService.php`
- Verify: `app/src/Service/Cms/CmsPageSaveService.php`
- Verify: `app/src/Repository/PageRepository.php`

**Step 1: Manually test dance home save**

Check:
- page title updates
- section text updates
- pass rows update correctly

**Step 2: Manually test dance detail save**

Check:
- hero images update
- highlight rows update
- tracks update
- existing audio URLs remain preserved when not replaced

**Step 3: Manually test invalid IDs**

Check that invalid section item ids still fail with a clear error, rather than silently skipping updates.

**Step 4: Compare query counts before and after**

At minimum, confirm the following expected reductions:
- no extra slug-to-page-id lookup if direct page id save path is implemented
- one upfront section-item id lookup replaces per-item fallback existence checks

