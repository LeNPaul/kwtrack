"use strict";

function getUserInfo() {
  return new Promise(function(e, n) {
    $.ajax({
      type: "POST",
      url: URL_CONFIG.GETUSERINFO,
      contentType: "application/json",
      success: function(n) {
        e(n)
      },
      error: function(e) {
        n(1)
      }
    })
  })
}

function updateUserBox() {
  getUserInfo().then(function(e) {
    localStorage.setItem("amz_isSubscriber", e.isSubscriber ? "1" : "-1"), $(".amz_notSubscribe_table_data").hide(), e.isSubscriber ? ($(".btn-amz-upgrade").hide(), $("#check-sponsored").attr("disabled", !1), $("#check-sponsored").attr("checked", "checked"), $(".tips-unlogin-box").hide(), $(".tips-login-unsubscriber-box").hide(), $(".tips-login-subscriber-box").show()) : ($(".btn-amz-upgrade").show(), $("#check-sponsored").attr("disabled", "disabled"), $("#check-sponsored").attr("checked", !1), $(".tips-unlogin-box").hide(), $(".tips-login-unsubscriber-box").show(), $(".tips-login-subscriber-box").hide()), localStorage.setItem("kw_status", e.status), $("#userinfo-box #userinfo-box-username").text(e.username), $("#userinfo-box").css("display", "inline-block"), $("#login-box").hide(), 1 !== e.status && $("#pay-btn").show()
  })["catch"](function(e) {
    $(".btn-amz-upgrade").show(), $("#check-sponsored").attr("disabled", "disabled"), $("#check-sponsored").attr("checked", !1), $("#userinfo-box").hide(), $("#login-box").css("display", "inline-block"), $(".tips-unlogin-box").show(), $(".tips-login-unsubscriber-box").hide(), $(".tips-login-subscriber-box").hide()
  })
}

function login(e) {
  return new Promise(function(n, t) {
    $.ajax({
      type: "POST",
      url: URL_CONFIG.LOGIN,
      contentType: "application/json",
      data: JSON.stringify(e),
      success: function(e) {
        n(e)
      },
      error: function(e) {
        t(1)
      }
    })
  })
}

function logout() {
  return new Promise(function(e, n) {
    $.ajax({
      type: "POST",
      url: URL_CONFIG.LOGOUT,
      contentType: "application/json",
      success: function(n) {
        e(n)
      },
      error: function(e) {
        n(1)
      }
    })
  })
}
var _typeof6 = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
      return typeof e
    } : function(e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
    },
    _typeof5 = "function" == typeof Symbol && "symbol" === _typeof6(Symbol.iterator) ? function(e) {
      return "undefined" == typeof e ? "undefined" : _typeof6(e)
    } : function(e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : "undefined" == typeof e ? "undefined" : _typeof6(e)
    },
    _typeof4 = "function" == typeof Symbol && "symbol" === _typeof5(Symbol.iterator) ? function(e) {
      return "undefined" == typeof e ? "undefined" : _typeof5(e)
    } : function(e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : "undefined" == typeof e ? "undefined" : _typeof5(e)
    },
    _typeof3 = "function" == typeof Symbol && "symbol" === _typeof4(Symbol.iterator) ? function(e) {
      return "undefined" == typeof e ? "undefined" : _typeof4(e)
    } : function(e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : "undefined" == typeof e ? "undefined" : _typeof4(e)
    },
    _typeof2 = "function" == typeof Symbol && "symbol" === _typeof3(Symbol.iterator) ? function(e) {
      return "undefined" == typeof e ? "undefined" : _typeof3(e)
    } : function(e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : "undefined" == typeof e ? "undefined" : _typeof3(e)
    },
    _typeof = "function" == typeof Symbol && "symbol" === _typeof2(Symbol.iterator) ? function(e) {
      return "undefined" == typeof e ? "undefined" : _typeof2(e)
    } : function(e) {
      return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : "undefined" == typeof e ? "undefined" : _typeof2(e)
    },
    concat = function(e, n) {
      return e.concat(n)
    },
    flatMap = function(e, n) {
      return n.map(e).reduce(concat, [])
    };
Array.prototype.flatMap = function(e) {
  return flatMap(e, this)
}, jQuery.extend(jQuery.fn.dataTableExt.oSort, {
  "numeric-pre": function(e) {
    var n = String(e).replace(/<[\s\S]*?>/g, "");
    return n = n.replace(/Not in top 300/g, "100000"), n = parseInt(n) || 0, parseInt(n)
  },
  "numeric-asc": function(e, n) {
    return e < n ? -1 : e > n ? 1 : 0
  },
  "numeric-desc": function(e, n) {
    return e < n ? 1 : e > n ? -1 : 0
  }
}), jQuery.extend(jQuery.fn.dataTableExt.oSort, {
  "html-percent-pre": function(e) {
    var n = String(e).replace(/<[\s\S]*?>/g, "");
    if (n = n.replace(/Not in top 300/g, "100000"), n = n.replace(/ /gi, ""), n.indexOf("Page") !== -1 && n.indexOf("No") !== -1) {
      var t = n.substring(n.indexOf("Page") + 4, n.indexOf("No"));
      t = parseInt(t) || 0;
      var o = n.substr(n.indexOf("No") + 2, 2);
      return o = parseInt(o) || 0, 20 * t + o
    }
    return parseInt(n)
  },
  "html-percent-asc": function(e, n) {
    return e < n ? -1 : e > n ? 1 : 0
  },
  "html-percent-desc": function(e, n) {
    return e < n ? 1 : e > n ? -1 : 0
  }
});
var BASE_SERVER = "https://amzdatastudio.com",
    BASE_API_SERVER = "https://api.amzdatastudio.com",
    URL_CONFIG = {
      LOGIN: BASE_API_SERVER + "/api/user/login",
      LOGOUT: BASE_API_SERVER + "/api/user/logout",
      GETUSERINFO: BASE_API_SERVER + "/api/chrome-extension-kw-index/userinfo",
      KWSUGGEST: BASE_API_SERVER + "/api/chrome-extension-kw-index/relevant-kw"
    };
$("#userinfo-box-username").attr("href", BASE_SERVER + "/user"), $("a.amz-subscriber-page").attr("href", BASE_SERVER + "/user/pricing?type=subscription"), $("a.kwindex-pricing-page").attr("href", BASE_SERVER + "/user/kwindex-extension-pricing"), $("a.kwindex-intro-page").attr("href", "https://amzdatastudio.com/amazon-keyword-relevance-score/"), $(document).ready(function() {
  $(function() {
    $('[data-toggle="tooltip"]').tooltip()
  }), updateUserBox();
  var e = $("#datatable").DataTable({
    dom: "Bfrtip",
    buttons: ["excelHtml5", "csvHtml5"],
    data: [],
    columnDefs: [{
      targets: 4,
      orderable: !1
    }],
    aoColumnDefs: [{
      sType: "html-percent",
      aTargets: [5]
    }],
    paging: !1,
    rowCallback: function(e) {
      "Y" === data[2] ? $(e).css("background-color", "#dff0d8") : $(e).css("background-color", "#f2dede")
    }
  });
  $("#datatable").dataTable().fnSetColumnVis(8, !1), $("#datatable").css("width", "60rem");
  var n = $("#progress-bar"),
      t = $("#progress-bar-text"),
      o = [],
      r = function(e) {
        return new Promise(function(n, t) {
          0 === e.length ? n([]) : $.ajax({
            type: "GET",
            url: "https://www.amazon." + $("#country").val() + "/dp/" + encodeURIComponent(e),
            success: function(t) {
              var o = [];
              try {
                var r = t.match(/asinVariationValues" :(.*)\n/)[1];
                r = r.substring(0, r.length - 1);
                var a = JSON.parse(r);
                o = Object.keys(a);
                var i = t.match(/parentAsin" : "(.*)",\n/)[1];
                i && o.push(i)
              } catch (s) {}
              o.push(e), n(o)
            },
            error: function(e) {
              e && e.responseText.indexOf("amazon") !== -1 && $("#search-btn").prop("disabled") && (window.alert("Please input a valid ASIN, or leave the ASIN field empty to retrieve the keywords' data only."), $("#search-btn").prop("disabled", !1), $("#search-btn").html("Check Now")), t([])
            }
          })
        })
      },
      a = function() {
        var e = !(arguments.length > 0 && void 0 !== arguments[0]) || arguments[0],
            a = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
        $("#kw-btn").prop("disabled", !0), $("#kw-btn").html("Loading"), l([], e);
        var c = $("#asin").val().trim(),
            u = 0 === c.length && $("#check-volume").is(":checked") || null !== c.match(/(?:[0-9A-Z]{10})/i),
            d = "" !== $("#keywords").val();
        if (d)
          if (u) {
            o = [];
            var p = $("#keywords").val().split(/\n/).map(function(e) {
                  return e.trim()
                }).filter(function(e) {
                  return e.length > 0
                }),
                f = [];
            if ($.each(p, function(e, n) {
                $.inArray(n, f) === -1 && f.push(n)
              }), f.length > 200) return alert("Please donâ€™t put more that 200 lines for one search"), $("#kw-btn").prop("disabled", !1), void $("#kw-btn").html("Relevant KW");
            $("#search-btn").prop("disabled", !0), $("#search-btn").html("Loading"), t.html("0%"), n.width("0%");
            var h = $("#check-rank").is(":checked"),
                b = {};
            r(c).then(function(r) {
              f.forEach(function(a, u) {
                function d(e, o) {
                  b[e] = o;
                  var r = 0;
                  for (var a in b) r += b[a] || 0;
                  var i = Math.round(100 * r / f.length / 20);
                  i = i > 99 ? 99 : i, t.html(i + "%"), n.width(i + "%")
                }

                function p(e) {
                  $.ajax({
                    type: "GET",
                    url: "https://www.amazon." + $("#country").val() + "/s/",
                    data: "field-keywords=" + encodeURIComponent(a) + "&page=1",
                    success: function(n) {
                      var t = $($.parseHTML(n)).find("#s-result-count").length > 0 ? $($.parseHTML(n)).find("#s-result-count") : $($.parseHTML(n)).find(".rush-component .s-desktop-toolbar .sg-row-align-items-center .a-spacing-small SPAN"),
                          o = "0";
                      if (t && t.length > 0) {
                        var r = t[0].innerText.toLowerCase();
                        if ($("#country").val().indexOf("jp") === -1) {
                          var a = /([\d,.\s]+) (result|rÃ©sult|risult|ergebnissen|ergebnisse)/i;
                          try {
                            o = a.exec(r.toLowerCase())[1]
                          } catch (i) {}
                        } else o = r.indexOf("ä»¥ä¸Š") !== -1 ? r.substring(r.indexOf("æ¤œç´¢çµæžœ"), r.indexOf("ä»¥ä¸Š")) : r.substring(r.indexOf("æ¤œç´¢çµæžœ") === -1 ? 0 : r.indexOf("æ¤œç´¢çµæžœ"), r.indexOf("ã®ã†ã¡"))
                      }
                      o = o.replace(/[^0-9]/g, ""), o = parseInt(o) || 0, o > 0 && o % 1e3 === 0 && (o += "+"), e(o)
                    }
                  })
                }

                function m(n, t, r, c, d, p) {
                  var h = c,
                      b = "https://www.amazon." + $("#country").val() + "/dp/" + encodeURIComponent(h),
                      m = h ? "<a href='" + b + "' target='_blank'>" + h + "</a>" : "-",
                      y = null === d ? "-" : d ? "Y" : "N",
                      g = r;
                  if (r.indexOf("Page") !== -1) {
                    var w = parseInt(r.substring(r.indexOf("page") + 5, r.indexOf("<br"))) || 1,
                        v = "https://www.amazon." + $("#country").val() + "/s/?field-keywords=" + encodeURIComponent(a) + "&page=" + w;
                    g = '<a href="' + v + '" target="_blank">' + r + "</a>"
                  }
                  var S = i[a] || "-";
                  S = '<span style="color: #FFA500;">' + S + "</span>", o.push([u + 1, a, n, m, t, g, y, p, S, "", ""]), f.length >= 10 ? o.length % 10 === 0 && l(o, e) : l(o, e), o.length === f.length && s(o, e)
                }

                function y(e, n, t, o, r) {
                  var i = "Not in top 300" !== n;
                  i ? (i = i ? "Y" : "N", m(i, n, t, e, o, r)) : $.ajax({
                    type: "GET",
                    url: "https://www.amazon." + $("#country").val() + "/s/",
                    data: "field-keywords=" + encodeURIComponent(c) + "+" + encodeURIComponent(a),
                    success: function(s) {
                      i = 0 === $($.parseHTML(s)).find("#noResultsTitle").length, i ? (i = i ? "Y" : "N", m(i, n, t, e, o, r)) : $.ajax({
                        type: "GET",
                        url: "https://www.amazon." + $("#country").val() + "/s/",
                        data: "field-keywords=" + encodeURIComponent(a) + "+" + encodeURIComponent(c),
                        success: function(a) {
                          i = 0 === $($.parseHTML(a)).find("#noResultsTitle").length, i = i ? "Y" : "N", m(i, n, t, e, o, r)
                        }
                      })
                    },
                    error: function() {
                      console.error("Server Error")
                    }
                  })
                }

                function g(e, n, t, o) {
                  var r = arguments.length > 4 && void 0 !== arguments[4] ? arguments[4] : null;
                  $.ajax({
                    type: "GET",
                    url: "https://www.amazon." + $("#country").val() + "/s/",
                    data: "field-keywords=" + encodeURIComponent(n) + "&page=" + t,
                    success: function(a) {
                      if (null === r) {
                        var i = $($.parseHTML(a)).find("#s-result-count").length > 0 ? $($.parseHTML(a)).find("#s-result-count") : $($.parseHTML(a)).find(".rush-component .s-desktop-toolbar .sg-row-align-items-center .a-spacing-small SPAN"),
                            s = "0";
                        if (i && i.length > 0) {
                          var l = i[0].innerText.toLowerCase();
                          if ($("#country").val().indexOf("jp") === -1) {
                            var c = /([\d,.\s]+) (result|rÃ©sult|risult|ergebnissen|ergebnisse)/i;
                            try {
                              s = c.exec(l.toLowerCase())[1]
                            } catch (u) {}
                          } else s = l.indexOf("ä»¥ä¸Š") !== -1 ? l.substring(l.indexOf("æ¤œç´¢çµæžœ"), l.indexOf("ä»¥ä¸Š")) : l.substring(l.indexOf("æ¤œç´¢çµæžœ") === -1 ? 0 : l.indexOf("æ¤œç´¢çµæžœ"), l.indexOf("ã®ã†ã¡"))
                        }
                        s = s.replace(/[^0-9]/g, ""), s = parseInt(s) || 0, s > 0 && s % 1e3 === 0 && (s += "+"), r = s
                      }
                      var p = !1,
                          f = 1,
                          h = null,
                          b = null,
                          m = null,
                          y = null;
                      if ($("#check-sponsored").is(":checked")) {
                        var w = $($.parseHTML(a)).find(".s-result-item").get().filter(function(e) {
                          return 0 === $(e).find(".acs-showcase-result-item-amazons-choice").length && $(e).attr("data-asin") && $(e).attr("class").indexOf("acs-private-brands-container-background") === -1
                        });
                        w.forEach(function(n) {
                          var o = $(n).attr("data-asin");
                          p || (e.includes(o) && (p = !0, h = o, b = $(n).find(".s-sponsored-header").length > 0 || $(n).text().toLowerCase().match(/Sponsored|Gesponsert|Gesponsord|Sponsorowane|Sponsorlu|SponzorovÃ¡no|SponsorisÃ©|Patrocinado|Sponsorizzato|Patrocinado/gi) || $(n).text().toLowerCase().indexOf("å•†å“æŽ¨å¹¿") !== -1 || $(n).text().toLowerCase().indexOf("ã‚¹ãƒãƒ³ã‚µãƒ¼ ãƒ—ãƒ­ãƒ€ã‚¯ãƒˆ") !== -1, m = (t - 1) * w.length + f, y = "Page " + t + "<br>No " + f), f++)
                        })
                      } else {
                        var w = $($.parseHTML(a)).find(".s-result-item").get().filter(function(e) {
                          var n = 0 === $(e).find(".s-sponsored-header").length && !$(e).text().toLowerCase().match(/Sponsored|Gesponsert|Gesponsord|Sponsorowane|Sponsorlu|SponzorovÃ¡no|SponsorisÃ©|Patrocinado|Sponsorizzato|Patrocinado/gi) && $(e).text().toLowerCase().indexOf("å•†å“æŽ¨å¹¿") === -1 && $(e).text().toLowerCase().indexOf("ã‚¹ãƒãƒ³ã‚µãƒ¼ ãƒ—ãƒ­ãƒ€ã‚¯ãƒˆ") === -1 && 0 === $(e).find(".acs-showcase-result-item-amazons-choice").length && $(e).attr("data-asin") && $(e).attr("class").indexOf("acs-private-brands-container-background") === -1;
                          return n
                        }).map(function(e) {
                          return $(e).attr("data-asin")
                        });
                        w.forEach(function(n) {
                          p || (e.includes(n) && (p = !0, h = n, m = (t - 1) * w.length + f, y = "Page " + t + "<br>No " + f), f++)
                        })
                      }
                      d(n, t), p ? (d(n, 20), o(h, m, y, b, r)) : t >= 20 ? o(h, "Not in top 300", "Not in top 300", b, r) : g(e, n, t + 1, o, r)
                    },
                    error: function() {
                      console.error("Server Error")
                    }
                  })
                }

                function w() {
                  if (0 === c.length) p(function(e) {
                    m("", "", "", "", null, e)
                  });
                  else {
                    var e = h ? r : [c];
                    g(e, a, 1, function(e, n, t, o, r) {
                      y(e, n, t, o, r)
                    })
                  }
                }
                w()
              })
            })
          } else window.alert("Please input a valid ASIN, or leave the ASIN field empty to retrieve the keywords' data only."), $("#kw-btn").prop("disabled", !1), $("#kw-btn").html("Relevant KW");
        else a.reverseTips ? window.alert(a.reverseTips) : window.alert("Please put keywords in the keywords field."), $("#kw-btn").prop("disabled", !1), $("#kw-btn").html("Relevant KW")
      },
      i = {};
  $("#logout-btn").click(function() {
    logout().then(function(e) {
      updateUserBox()
    })
  }), $("#login-btn").click(function() {
    $("#form-username").val().length > 0 && $("#form-password").val().length > 0 ? login({
      username: $("#form-username").val(),
      password: $("#form-password").val()
    }).then(function(e) {
      "error" === e.result ? alert(e.message) : (updateUserBox(), $("#loginModal").modal("hide"))
    })["catch"](function(e) {
      alert("Login failed: Wrong Username or Password"), console.error("failed to login, please try again")
    }) : alert("Invalid Username or Password")
  }), $("#pay-btn").click(function() {
    window.open(BASE_SERVER + "/user/kwindex-extension-pricing")
  }), $("#signup-btn").click(function() {
    window.open(BASE_SERVER + "/user/signup")
  }),

    $("#kw-btn").click(function() {
    return $("#asin").val().length < 10 ? void alert("Please input a valid ASIN") : void chrome.cookies.getAll({
      domain: ".amazon." + $("#country").val()
    }, function(e, n) {
      var t = e.map(function(e) {
            if (e.domain == ".amazon." + $("#country").val()) return e.name + "=" + e.value
          }),
          o = t.join(";").split("").reverse().join(""),
          r = $("#asin").val(),
          s = $("#country").val();

      console.log(o);

      $("#keywords").val(""), $.ajax({
        type: "POST",
        url: URL_CONFIG.KWSUGGEST,
        contentType: "application/json",
        data: JSON.stringify({
          asin: r,
          amzdatastudio_key: o,
          region: s
        }),
        success: function(e) {
          if (e.error) e.isSubscriber ? $("#subscriberTipsModal").modal("show") : $("#unsubscriberTipsModal").modal("show"), $("#kw-btn").prop("disabled", !1), $("#kw-btn").html("Relevant KW");
          else {
            var n = e.result;
            e.needLogin ? alert("Please sign into your SellerCentral to get the relevant keywords.") : 0 === n.length ? alert("No relevant keywords found. Please make sure you have input a valid ASIN.") : (n.forEach(function(e) {
              i[e.keyword] = e.score
            }), $("#keywords").val(n.map(function(e) {
              return e.keyword
            }).join("\n")), a(!0))
          }
        },
        error: function(e) {
          $("#loginTipsModal").modal("show")
        }
      })
    })
  }), $("#show-reversekw-tips").click(function(e) {
    $("#reversekwTipsModal").modal("show")
  }), $("#show-sponsor-tips").click(function(e) {
    $("#sponsorTipsModal").modal("show")
  }), $(".btn-sign-in").click(function() {
    $("#loginTipsModal").modal("hide"), $("#reversekwTipsModal").modal("hide"), $("#sponsorTipsModal").modal("hide"), $("#loginModal").modal("show")
  }), $("#reset-btn").click(function() {
    $("#asin").val(""), $("#keywords").val("")
  }),

    $("#search-btn").click(function() { console.log("asdfasdfasdf");
    i = {}, a(!1)
  });
  var s = function(e, o) {
        if ($("#check-volume").is(":checked"))
          console.log("Asdf");
          $.ajax({
          type: "POST",
          url: "https://sellercentral.amazon." + $("#country").val() + "/sspa/hsa/cm/keywords/power",
          contentType: "application/json",
          data: JSON.stringify({
            pageId: "https://www.amazon." + $("#country").val() + "/HSA/pages/default",
            keywordList: e.flatMap(function(e) {
              return [{
                key: e[1],
                matchType: "EXACT"
              }, {
                key: e[1],
                matchType: "BROAD"
              }]
            })
          }),
          success: function(r) {
            console.log(r);
            "object" === ("undefined" == typeof r ? "undefined" : _typeof(r)) && 0 !== r.length || alert("Please sign into your SellerCentral to get the Search Volume data.");
            var a = !0,
                i = !1,
                s = void 0;
            try {
              for (var c, u = e[Symbol.iterator](); !(a = (c = u.next()).done); a = !0) {
                var d = c.value,
                    p = null,
                    f = null;
                try {
                  p = r.find(function(e) {
                    return e.keyword.toLowerCase() === d[1].toLowerCase() && "EXACT" === e.matchType
                  }), f = r.find(function(e) {
                    return e.keyword.toLowerCase() === d[1].toLowerCase() && "BROAD" === e.matchType
                  })
                } catch (h) {}
                d[d.length - 2] = p ? Math.round(30 * p.impression) : "NONE", d[d.length - 1] = f ? Math.round(30 * f.impression) : "NONE"
              }
            } catch (b) {
              i = !0, s = b
            } finally {
              try {
                !a && u["return"] && u["return"]()
              } finally {
                if (i) throw s
              }
            }
            $("#search-btn").prop("disabled", !1), $("#search-btn").html("Check Now"), $("#kw-btn").prop("disabled", !1), $("#kw-btn").html("Relevant KW"), "1" !== localStorage.getItem("amz_isSubscriber") && $(".amz_notSubscribe_table_data").show(), n.width("100%"), t.html("Finished!"), l(e, o)
          }
        });
        else {
          var r = !0,
              a = !1,
              i = void 0;
          try {
            for (var s, c = e[Symbol.iterator](); !(r = (s = c.next()).done); r = !0) {
              var u = s.value;
              u.push(""), u.push("")
            }
          } catch (d) {
            a = !0, i = d
          } finally {
            try {
              !r && c["return"] && c["return"]()
            } finally {
              if (a) throw i
            }
          }
          $("#search-btn").prop("disabled", !1), $("#search-btn").html("Check Now"), $("#kw-btn").prop("disabled", !1), $("#kw-btn").html("Relevant KW"), n.width("100%"), t.html("Finished!"), l(e, o)
        }
      },
      l = function(n) {
        var t = arguments.length > 1 && void 0 !== arguments[1] && arguments[1];
        e = $("#datatable").DataTable({
          destroy: !0,
          dom: "Bfrtip",
          buttons: [{
            extend: "excelHtml5",
            title: $("#asin").val()
          }, {
            extend: "csvHtml5",
            title: $("#asin").val()
          }],
          data: n,
          aoColumnDefs: [{
            sType: "numeric",
            aTargets: [4]
          }, {
            sType: "html-percent",
            aTargets: [5]
          }, {
            sType: "numeric",
            aTargets: [7]
          }],
          paging: !1,
          rowCallback: function(e, n, t) {
            "Y" === n[2] ? $(e).css("background-color", "#dff0d8") : "N" === n[2] && $(e).css("background-color", "#f2dede")
          }
        }), $("#datatable").dataTable().fnSetColumnVis(8, t), t ? ($("#mainTable").removeClass("main-container"), $("#datatable").css("width", "72rem")) : ($("#datatable").css("width", "60rem"), $("#mainTable").addClass("main-container"))
      }
});