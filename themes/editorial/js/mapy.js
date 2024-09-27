// mapy.cz logo
MapyLogo = L.Control.extend({
    options: {
        position: 'bottomleft',
    },
    onAdd: function (map) {
        const container = L.DomUtil.create('div');
        const link = L.DomUtil.create('a', '', container);
        link.setAttribute('href', 'http://mapy.cz/');
        link.setAttribute('target', '_blank');
        link.innerHTML = '<img src="https://api.mapy.cz/img/api/logo.svg" />';
        L.DomEvent.disableClickPropagation(link);
        return container;
    },
});

// tlacitko Zobrazit na Mapy.cz
MapyLink = L.Control.extend({
    options: {
        position: 'topright',
        latitude: 0,
        longitude: 0
    },
    onAdd: function (map) {
        const container = L.DomUtil.create('div');
        const link = L.DomUtil.create('a', '', container);
        link.setAttribute('href', 'http://mapy.cz/turisticka?x=' + this.options.longitude + '&y=' + this.options.latitude + '&z=15&source=coor&id=' + this.options.longitude + '%2C' + this.options.latitude);
        link.setAttribute('target', '_blank');
        link.innerHTML = 'Zobrazit na Mapy.cz';
        link.style.cssText = "display:block; padding:4px 8px;"+
            "background-color:#e65a51; border:medium none;"+
            "border-radius:3px;"+
            "box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.3);"+
            "text-decoration:none; color:#fff;font-size:16px";
        L.DomEvent.disableClickPropagation(link);
        return container;
    },
});

// mapy graficke meritko
MapyMeritko = L.Control.extend({
    options: {
        position: "bottomleft",
        maxWidth: 300,
        metric: 1,
        imperial: 0,
        updateWhenIdle: 0
    },
    onAdd: function (t) {
        this._map = t;
        var base = "mapy-meritko",
            container = L.DomUtil.create("div", base),
            opts = this.options,
            plocha = L.DomUtil.create("div", base + "-plocha", container);
        this._addScales(opts, base, container),
        this.ScaleContainer = container;
        L.DomUtil.create("div", base + "-cerna " + base + "-cast1", plocha),
        L.DomUtil.create("div", base + "-cerna " + base + "-cast2", plocha);
        t.on(opts.updateWhenIdle ? "moveend" : "move", this._update, this),
        t.whenReady(this._update, this);
        return container
    },
    onRemove: function (t) {
        t.off(
            this.options.updateWhenIdle ? "moveend" : "move",
            this._update, this
        )
    },
    _addScales: function (t, e, i) {
        var popis = e + "-popis",
            cislo = popis + " " + e + "-cislo";
        this._iScale = L.DomUtil.create("div", popis + "-div", i),
        this._iScaleLabel = L.DomUtil.create("div", popis, this._iScale),
        this._iScaleNum1 = L.DomUtil.create("div", cislo + "1", this._iScale),
        this._iScaleNum2 = L.DomUtil.create("div", cislo + "2", this._iScale),
        this._iScaleNum3 = L.DomUtil.create("div", cislo + "3", this._iScale)
    },
    _update: function () {
        var mb = this._map.getBounds(),
            c = mb.getCenter().lat,
            i = 6378137 * Math.PI * Math.cos(c * Math.PI / 180),
            n = i * (mb.getNorthEast().lng - mb.getSouthWest().lng) / 180,
            ms = this._map.getSize(),
            opts = this.options,
            a = 00;
        ms.x > 0 && (a = n * (opts.maxWidth / ms.x)),
        this._updateScales(opts, a)
    },
    _updateScales: function (t, e) {
        t.metric && e && this._updateUnit(e, 1, 1000, "km", "m"),
        t.imperial && e && this._updateUnit(e, 3.2808399, 5280, "mi", "ft")
    },
    _updateUnit: function (t, p, o, j1, j2) {
        var a, ra1, ra2, ra3,
            n1 = this._iScaleNum1,
            n2 = this._iScaleNum2,
            n3 = this._iScaleNum3,
            l = this._iScale,
            n0 = this._iScaleLabel;
        t > o / 2 ? (a = t * p / o, u = " " + j1) : (a = t * p, u = " " + j2),
        ra3 = this._getRoundNum(a),
        ra1 = Math.round(ra3 / 3 * 10) / 10,
        ra2 = Math.round(ra3 / 3 * 20) / 10,
        l.style.width = this._getScaleWidth(ra3 / a) + "px",
        n0.innerHTML = "0",
        n1.innerHTML = ra1,
        n2.innerHTML = ra2,
        n3.innerHTML = ra3 + u
    },
    _getScaleWidth: function (t) {
        return Math.round(this.options.maxWidth * t) - 10
    },
    _getRoundNum: function (t) {
        if (t >= 1) {
            var e = Math.pow(10, (Math.floor(t) + "").length - 1),
                i = t / e;
            return i = i >= 9.6 ? 9.6 : i >= 9 ? 9 : i>= 7.5 ? 7.5 : i >= 6 ? 6 : i >= 4.8 ? 4.8 : i >= 4.5 ? 4.5 : i >= 3.6 ? 3.6 : i >= 3 ? 3 : i >= 2.4 ? 2.4 : i >= 1.8 ? 1.8 : i >= 1.5 ? 1.5 : i >= 1.2 ? 1.2 : 0.9, e * i
        }
        return (Math.round(100 * t) / 100).toFixed(1)
    }
});
