var a = { diskquota: "500", diskused: "250" };
var b = { diskquota: "1000", diskused: "100" };
var quotaA = parseFloat(a._diskquota || a.diskquota || 0) || 0;
var quotaB = parseFloat(b._diskquota || b.diskquota || 0) || 0;
var usedA = parseFloat(a._diskused || a.diskused || 0);
var usedB = parseFloat(b._diskused || b.diskused || 0);
var va = quotaA > 0 ? Math.min(Math.round((usedA / quotaA) * 100), 100) : 0;
var vb = quotaB > 0 ? Math.min(Math.round((usedB / quotaB) * 100), 100) : 0;
console.log(va, vb);
