$(function() {

  $("#search-btn").on("click", function() {
    console.log("Asdfasdfasdf");
    var kw = $("#keyword").text();
    $.ajax({
      type: "POST",
      url: "https://sellercentral.amazon.com/sspa/hsa/cm/keywords/power",
      contentType: "application/json",
      data: JSON.stringify({
        pageId: "https://www.amazon.com/HSA/pages/default",
        keywordList:
          [{
            key: kw,
            matchType: "EXACT"
          }, {
            key: kw,
            matchType: "BROAD"
          }]
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
      },
      error: function(d) {
        console.log(d);
      }
    });
  });

});

