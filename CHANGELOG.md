<!---
# CHANGELOG.md
# it-vends
# -->

v1.4.0
------
  * Add new "title" format.
  * Add new vendables
  * Restructure README & CHANGELOG into Markdown

v1.3.0
------
  * Improve redirect behaviour
  * Update to newer jQuery
  * Multiple typofixes & additional items
  * Higher ITEMLIMIT for butt scale
  * Benchmarking support
  * Add "bag" page for container support.

v1.2.0
------
  * Add Github & Google+ shortlinks to .htaccess(merged from v1.1.1)
  * Improve comments in .htaccess(merged from v1.1.1)

v1.2-rc1
------
  * common.php: Use correct array when vending special items
  * Increase rate of special items to 10%

v1.2-a2
------
  * Upgrade jQuery to 1.7.1
  * Add STYLE.txt containing style guidelines
  * Plethora of changes to meet style guidelines
  * Correct name for Eugene in README
  * Add .txt extensions to README/CHANGELOG/etc
  * vend.php: set $format once, using a single call to arg()
  * Vend list: separate items into "normal" and special" arrays.

v1.2-a1
------
  * Remove extraneous information from tail of GPL text
  * Implement timed fading-out of the "IT VENDS! <item>" segment
  * Add the jQuery doTimeout plugin to support fadeout

v1.1.1
------
  * Add Github & Google+ shortlinks to .htaccess
  * Improve comments in .htaccess

v1.1.0
------
  * Minor vend list modifications

v1.1-b2
------
  * Fix logic bug that lead to special items never being vend()ed
  * Fix typo in README
  * Changes to inlined JS for readability & W3C validation
  * Fix favicon - use PNG instead of ico
  * JS fix - work from front of array instead of end
  * Eliminate .php file suffix from request paths
  * Simplify DOM & generation thereof
  * Consolidate CSS

v1.1-b1
------
  * Lots of cleanup!
-Consistent tab-usage & spacing
-Change all string-delineating ' to "
-Get rid of PHP short tags
-Consistent braces
  * Add many comments
  * Refactoring
-Move vending action into a vend() function
  * Move functions.php to common.php
-Move over common variables & constants needed throughout
  * Add support for special items
  * Rename post_get() to arg()

v1.1-a2
------
  * Expand items in vend list
  * Remove contributions from Authors listing in README
  * Upgrade jQuery to 1.6.4
  * Move Changelog from README to CHANGELOG
  * Simplify vend.php's output generation to use a single for() loop
  * Update htaccess to allow ipv4/6 and testing subdomains
  * Enhance cross-domain compatibility
  * Add code commenting
  * Switch to 307 redirects to fix caching issues
  * Change default link to use GET instead of POST
  * Load the primary "Vend" button via AJAX instead of a full POST

v1.1-a1
------
  * First revision of the REST API
      * Add multiple output formats, defaulting to "text"
      * Add support for multiple items being vended, max of 10 per request
  * Add a separate functions file
  * Improve handling of variables from POST and GET
  * Refactor copyright notices
      * Change file header copyright notices
      * Rename Contributors section of README to Authors
      * Move primary Authors into Authors section
  * Add License clauses
      * Add requirement to rebrand to preserve 'It Vends' mark
      * Add requirement to retain Authors and License information
      * Add explicit permission for Non-attribution when used as a service
  * Add Kindari(William Cahill-Manley) to the Primary Authors list

v1.0.2
------
  * Update external libs to their most recent versions
  * Expand the Items Pool

v1.0.1
------
  * Add Kyte to the Acknowledgements list
  * Typo corrections in the vendlist
  * Now passes W3C XHTML validation
  * Make the favicon.ico's path explicit in index.php

v1.0
------
  * First production release
  * Index.php: Provides a large "Vend!" button for web browser users
  * Vend.php: Basic Vending API for use by scripts, eg IRC bots.
  * IT VENDS!
