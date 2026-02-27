var emails = [
   { "diskusedpercent_float": 0, "diskquota": "unlimited", "diskused": "8328.27" },
   { "diskusedpercent_float": 53.33, "diskquota": "3000", "diskused": "1599.92" },
   { "diskusedpercent_float": 0.11, "diskquota": "8000.00", "diskused": "9.39" },
   { "diskusedpercent_float": 99.9, "diskquota": "500", "diskused": "499" }
];

var sorted = emails.slice().sort(function(a, b) {
    var va, vb;
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
    return vb - va; // desc
});

console.log(sorted.map(s => s.diskusedpercent_float));
