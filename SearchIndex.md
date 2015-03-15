The following describes the search index design.

# Introduction #
The goal of the search index is to provide a fast way to match user text in order to find an object. The search index combines all relational content and rolls it into be large chunk of text for scanning. This text is indexed for full text searching.

# Ranking #
Content ranking follows the simple concepts of popularity. The more relationships you have, the more important, or popular an object is.

# Category Weights #
Within a category organization, each object is assigned its weight within its organization via categories. The weight of the category assignment depends on the ordering in the perspective setup.

# Refreshing Search Index #
Search index is not built in real time, but through a batch process. This process can run depending on your import or update schedule. Nightly should do. Reference [search\_index\_cron\_job.sh](http://code.google.com/p/rati/source/browse/trunk/bin/search_index_cron_job.sh).