function w() {
}
function F(e) {
  return e();
}
function T() {
  return /* @__PURE__ */ Object.create(null);
}
function x(e) {
  e.forEach(F);
}
function J(e) {
  return typeof e == "function";
}
function D(e, t) {
  return e != e ? t == t : e !== t || e && typeof e == "object" || typeof e == "function";
}
function G(e) {
  return Object.keys(e).length === 0;
}
function p(e, t) {
  e.appendChild(t);
}
function K(e, t, n) {
  e.insertBefore(t, n || null);
}
function N(e) {
  e.parentNode && e.parentNode.removeChild(e);
}
function H(e, t) {
  for (let n = 0; n < e.length; n += 1)
    e[n] && e[n].d(t);
}
function m(e) {
  return document.createElement(e);
}
function P(e) {
  return document.createTextNode(e);
}
function A() {
  return P(" ");
}
function I(e, t, n, r) {
  return e.addEventListener(t, n, r), () => e.removeEventListener(t, n, r);
}
function b(e, t, n) {
  n == null ? e.removeAttribute(t) : e.getAttribute(t) !== n && e.setAttribute(t, n);
}
function Q(e) {
  return Array.from(e.childNodes);
}
function R(e, t) {
  t = "" + t, e.data !== t && (e.data = t);
}
let E;
function $(e) {
  E = e;
}
function U() {
  if (!E)
    throw new Error("Function called outside component initialization");
  return E;
}
function V(e) {
  U().$$.on_mount.push(e);
}
const v = [], L = [];
let k = [];
const M = [], W = /* @__PURE__ */ Promise.resolve();
let j = !1;
function X() {
  j || (j = !0, W.then(q));
}
function C(e) {
  k.push(e);
}
const O = /* @__PURE__ */ new Set();
let y = 0;
function q() {
  if (y !== 0)
    return;
  const e = E;
  do {
    try {
      for (; y < v.length; ) {
        const t = v[y];
        y++, $(t), Y(t.$$);
      }
    } catch (t) {
      throw v.length = 0, y = 0, t;
    }
    for ($(null), v.length = 0, y = 0; L.length; )
      L.pop()();
    for (let t = 0; t < k.length; t += 1) {
      const n = k[t];
      O.has(n) || (O.add(n), n());
    }
    k.length = 0;
  } while (v.length);
  for (; M.length; )
    M.pop()();
  j = !1, O.clear(), $(e);
}
function Y(e) {
  if (e.fragment !== null) {
    e.update(), x(e.before_update);
    const t = e.dirty;
    e.dirty = [-1], e.fragment && e.fragment.p(e.ctx, t), e.after_update.forEach(C);
  }
}
function Z(e) {
  const t = [], n = [];
  k.forEach((r) => e.indexOf(r) === -1 ? t.push(r) : n.push(r)), n.forEach((r) => r()), k = t;
}
const ee = /* @__PURE__ */ new Set();
function te(e, t) {
  e && e.i && (ee.delete(e), e.i(t));
}
function ne(e, t, n, r) {
  const { fragment: c, after_update: d } = e.$$;
  c && c.m(t, n), r || C(() => {
    const u = e.$$.on_mount.map(F).filter(J);
    e.$$.on_destroy ? e.$$.on_destroy.push(...u) : x(u), e.$$.on_mount = [];
  }), d.forEach(C);
}
function re(e, t) {
  const n = e.$$;
  n.fragment !== null && (Z(n.after_update), x(n.on_destroy), n.fragment && n.fragment.d(t), n.on_destroy = n.fragment = null, n.ctx = []);
}
function le(e, t) {
  e.$$.dirty[0] === -1 && (v.push(e), X(), e.$$.dirty.fill(0)), e.$$.dirty[t / 31 | 0] |= 1 << t % 31;
}
function ce(e, t, n, r, c, d, u, h = [-1]) {
  const f = E;
  $(e);
  const l = e.$$ = {
    fragment: null,
    ctx: [],
    // state
    props: d,
    update: w,
    not_equal: c,
    bound: T(),
    // lifecycle
    on_mount: [],
    on_destroy: [],
    on_disconnect: [],
    before_update: [],
    after_update: [],
    context: new Map(t.context || (f ? f.$$.context : [])),
    // everything else
    callbacks: T(),
    dirty: h,
    skip_bound: !1,
    root: t.target || f.$$.root
  };
  u && u(l.root);
  let _ = !1;
  if (l.ctx = n ? n(e, t.props || {}, (o, s, ...i) => {
    const g = i.length ? i[0] : s;
    return l.ctx && c(l.ctx[o], l.ctx[o] = g) && (!l.skip_bound && l.bound[o] && l.bound[o](g), _ && le(e, o)), s;
  }) : [], l.update(), _ = !0, x(l.before_update), l.fragment = r ? r(l.ctx) : !1, t.target) {
    if (t.hydrate) {
      const o = Q(t.target);
      l.fragment && l.fragment.l(o), o.forEach(N);
    } else
      l.fragment && l.fragment.c();
    t.intro && te(e.$$.fragment), ne(e, t.target, t.anchor, t.customElement), q();
  }
  $(f);
}
class ie {
  $destroy() {
    re(this, 1), this.$destroy = w;
  }
  $on(t, n) {
    if (!J(n))
      return w;
    const r = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
    return r.push(n), () => {
      const c = r.indexOf(n);
      c !== -1 && r.splice(c, 1);
    };
  }
  $set(t) {
    this.$$set && !G(t) && (this.$$.skip_bound = !0, this.$$set(t), this.$$.skip_bound = !1);
  }
}
function z(e, t, n) {
  const r = e.slice();
  return r[7] = t[n], r;
}
function B(e) {
  let t, n = (
    /*tab*/
    e[7].label + ""
  ), r, c, d, u;
  function h() {
    return (
      /*click_handler*/
      e[5](
        /*tab*/
        e[7]
      )
    );
  }
  return {
    c() {
      t = m("button"), r = P(n), b(t, "class", c = /*tab*/
      e[7].key == /*selectedKey*/
      e[0] ? "active" : "inactive");
    },
    m(f, l) {
      K(f, t, l), p(t, r), d || (u = I(t, "click", h), d = !0);
    },
    p(f, l) {
      e = f, l & /*tabs*/
      2 && n !== (n = /*tab*/
      e[7].label + "") && R(r, n), l & /*tabs, selectedKey*/
      3 && c !== (c = /*tab*/
      e[7].key == /*selectedKey*/
      e[0] ? "active" : "inactive") && b(t, "class", c);
    },
    d(f) {
      f && N(t), d = !1, u();
    }
  };
}
function se(e) {
  let t, n, r, c, d, u, h, f, l, _, o = (
    /*tabs*/
    e[1]
  ), s = [];
  for (let i = 0; i < o.length; i += 1)
    s[i] = B(z(e, o, i));
  return {
    c() {
      t = m("div"), n = m("div"), r = m("div"), c = m("div"), d = A();
      for (let i = 0; i < s.length; i += 1)
        s[i].c();
      u = A(), h = m("div"), f = A(), l = m("div"), b(c, "class", "placeholder"), b(h, "class", "placeholder"), b(r, "class", "tabbar"), b(l, "class", _ = `tabbar-stripe tabbar-stripe-${/*selectedKey*/
      e[0]}`), b(n, "class", "tabbar-wrapper"), b(t, "class", "tw");
    },
    m(i, g) {
      K(i, t, g), p(t, n), p(n, r), p(r, c), p(r, d);
      for (let a = 0; a < s.length; a += 1)
        s[a] && s[a].m(r, null);
      p(r, u), p(r, h), p(n, f), p(n, l);
    },
    p(i, [g]) {
      if (g & /*tabs, selectedKey, setTab*/
      7) {
        o = /*tabs*/
        i[1];
        let a;
        for (a = 0; a < o.length; a += 1) {
          const S = z(i, o, a);
          s[a] ? s[a].p(S, g) : (s[a] = B(S), s[a].c(), s[a].m(r, u));
        }
        for (; a < s.length; a += 1)
          s[a].d(1);
        s.length = o.length;
      }
      g & /*selectedKey*/
      1 && _ !== (_ = `tabbar-stripe tabbar-stripe-${/*selectedKey*/
      i[0]}`) && b(l, "class", _);
    },
    i: w,
    o: w,
    d(i) {
      i && N(t), H(s, i);
    }
  };
}
function oe(e, t, n) {
  let { props: r } = t, { helpers: c } = t;
  r.data;
  let d = "", u = [];
  function h(l) {
    n(0, d = l), r.onChange(d);
  }
  V((l) => {
    n(1, u = c.toJS(r.schemaOptions.tabs).value.map(({ name: _, title: o }) => ({ key: _, label: o }))), h(u[0].key);
  });
  const f = (l) => h(l.key);
  return e.$$set = (l) => {
    "props" in l && n(3, r = l.props), "helpers" in l && n(4, c = l.helpers);
  }, [d, u, h, r, c, f];
}
class ae extends ie {
  constructor(t) {
    super(), ce(this, t, oe, se, D, { props: 3, helpers: 4 });
  }
}
export {
  ae as default
};
