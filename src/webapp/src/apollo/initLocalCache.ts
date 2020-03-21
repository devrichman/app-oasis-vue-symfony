import { InMemoryCache } from 'apollo-cache-inmemory'

export function initLocalCache(cache: InMemoryCache) {
    cache.writeData({
        data: {
            filters: [],
        },
    });

    return cache;
}