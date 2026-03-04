Modules
-------

The Extension “varnish” can be used to invalid the Varnish Cache.
The extension has one backend module in Web > Varnish.

Web > Varnish:
^^^^^^^^^^^^^^

The backend module have three options, `Ban All TYPO3 Pages`, `Ban tag by name` and `Ban URLs by RegEx`.

..  figure:: /Images/varnish_be_module.png
    :alt: Varnish Backend Module

    Varnish Backend Module

Ban all TYPO3 Pages
...................

Clears all pages from the Varnish Cache, this can also be done with the "Flush Varnish Cache" in the Cache Menu

..  figure:: /Images/varnish_cache_clear.png
    :alt: Flush Varnish Cache

    Flush Varnish Cache


Ban tag by name
...............

The extension uses the tag convention like `page_<uid>` (page_1234), which can be use to invalidate/ban a single varnish
cache entry.

So if you want to invalide/ban the cache for Page UID 1234, you would type `page_1234` and click Ban Tag.

Ban URLs by RegEx
.................

This can be used to invalidate/ban caches based on Regular Expression. If one wants to invalidate/ban caches
for all pages where the URL starts with `/products`, it can be done with `^/products` and click `Ban by RegEx`.
