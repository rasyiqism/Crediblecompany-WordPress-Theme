/**
 * WebMCP Tools Module — Credible Company.
 * Registers client-side tools via navigator.modelContext.registerTool() on page load
 * when WebMCP is supported by the browser/in-browser agent.
 *
 * @package CredibleCompany
 */

(function () {
    'use strict';

    if (
        typeof navigator === 'undefined' ||
        !navigator.modelContext ||
        typeof navigator.modelContext.registerTool !== 'function'
    ) {
        return;
    }

    var data = window.ccWebMcpData || {};

    function toTextResult(payload) {
        return {
            content: [
                {
                    type: 'text',
                    text: typeof payload === 'string' ? payload : JSON.stringify(payload, null, 2)
                }
            ]
        };
    }

    function safeRegister(toolConfig) {
        try {
            navigator.modelContext.registerTool(toolConfig);
        } catch (err) {
            // Avoid breaking page scripts if a browser rejects duplicate or experimental registration.
            if (typeof console !== 'undefined' && console.debug) {
                console.debug('WebMCP tool registration skipped:', toolConfig.name, err);
            }
        }
    }

    safeRegister({
        name: 'search_site_content',
        description: 'Search published articles, pages, books, and testimonials on the website by keyword.',
        inputSchema: {
            type: 'object',
            properties: {
                query: {
                    type: 'string',
                    description: 'Keyword or phrase to search for.'
                },
                limit: {
                    type: 'integer',
                    description: 'Maximum number of results to return (default 5).'
                }
            },
            required: ['query']
        },
        execute: async function (args) {
            var query = args && args.query ? String(args.query).trim() : '';
            var limit = args && args.limit ? Math.min(20, Math.max(1, parseInt(args.limit, 10) || 5)) : 5;
            if (!query) {
                return toTextResult({ error: 'Missing required parameter: query' });
            }

            if (data.mcpEndpoint) {
                var res = await fetch(data.mcpEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        jsonrpc: '2.0',
                        id: 1,
                        method: 'tools/call',
                        params: {
                            name: 'search_site_content',
                            arguments: { query: query, limit: limit }
                        }
                    })
                });
                var json = await res.json();
                if (json && json.result) {
                    return json.result;
                }
            }

            if (data.searchRestUrl) {
                var url = data.searchRestUrl + '?search=' + encodeURIComponent(query) + '&per_page=' + limit;
                var wpRes = await fetch(url);
                var items = await wpRes.json();
                return toTextResult(items);
            }

            return toTextResult({ query: query, results: [] });
        }
    });

    safeRegister({
        name: 'get_publishing_packages',
        description: 'List available book publishing packages with IDR prices, copy quantities, book sizes, and included facilities.',
        inputSchema: {
            type: 'object',
            properties: {
                max_price_idr: {
                    type: 'number',
                    description: 'Optional maximum package price filter in IDR.'
                }
            }
        },
        execute: async function (args) {
            var packages = Array.isArray(data.packages) ? data.packages.slice() : [];
            if (args && typeof args.max_price_idr === 'number') {
                packages = packages.filter(function (pkg) {
                    if (!Array.isArray(pkg.prices_idr) || pkg.prices_idr.length === 0) {
                        return false;
                    }
                    return Math.min.apply(null, pkg.prices_idr) <= args.max_price_idr;
                });
            }
            return toTextResult(packages);
        }
    });

    safeRegister({
        name: 'get_contact_and_business_info',
        description: 'Get official company contact details (email, phone, office address) and links to About, Contact, Terms, Privacy, and Pricing pages.',
        inputSchema: {
            type: 'object',
            properties: {}
        },
        execute: async function () {
            return toTextResult(data.business || {});
        }
    });

    safeRegister({
        name: 'get_faqs',
        description: 'Retrieve frequently asked questions and answers about book publishing services, ISBN, and royalties.',
        inputSchema: {
            type: 'object',
            properties: {
                topic: {
                    type: 'string',
                    description: 'Optional keyword to filter FAQ entries.'
                }
            }
        },
        execute: async function (args) {
            var faqs = Array.isArray(data.faqs) ? data.faqs.slice() : [];
            var topic = args && args.topic ? String(args.topic).trim().toLowerCase() : '';
            if (topic) {
                faqs = faqs.filter(function (item) {
                    var haystack = ((item.q || '') + ' ' + (item.a || '')).toLowerCase();
                    return haystack.indexOf(topic) !== -1;
                });
            }
            return toTextResult(faqs);
        }
    });
})();
