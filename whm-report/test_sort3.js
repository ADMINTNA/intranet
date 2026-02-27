var emails = [
   { "_diskquota": "1000", "_diskused": "500" }, /* 50% */
   { "_diskquota": "1000", "_diskused": "100" }, /* 10% */
   { "_diskquota": null, "_diskused": "100" }     /* 0%  */
];

var sortKey = 'diskusedpercent';
var sortDir = 'desc';

var sorted = emails.slice().sort(function(a, b) {
    var va, vb;
    if (sortKey === 'diskusedpercent') {
        if ('diskusedpercent_float' in a && 'diskusedpercent_float' in b) {
            va = parseFloat(a.diskusedpercent_float || 0);
            vb = parseFloat(b.diskusedpercent_float || 0);
        } else {
            var parseQuota = function(q) {
                if (!q || q === 'unlimited' || q === 'None') return 0;
                return parseFloat(q) || 0;
            };
            var quotaA = parseQuota(a._diskquota) || parseQuota(a.diskquota);
            var quotaB = parseQuota(b._diskquota) || parseQuota(b.diskquota);
            var usedA = parseFloat(a._diskused || a.diskused || 0);
            var usedB = parseFloat(b._diskused || b.diskused || 0);
            va = quotaA > 0 ? Math.min((usedA / quotaA) * 100, 100) : 0;
            vb = quotaB > 0 ? Math.min((usedB / quotaB) * 100, 100) : 0;
        }
    }
    return sortDir === 'asc' ? va - vb : vb - va;
});

console.log(sorted.map(s => s._diskused));
