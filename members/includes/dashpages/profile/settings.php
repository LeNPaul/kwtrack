<?php


?>

<div class="row">
  
  <textarea class="form-control" id="keywords" placeholder="Separate your keywords with a new line (200 lines maximum)" rows="6"></textarea>
  <button id="search-btn">Get Volume</button>
  
  <p id="exact_volume"></p>
  <p id="broad_volume"></p>
  
  <h1>Settings</h1>

  <div class="col-12">
    <div class="card">
      <div class="card-header">
      </div>
      <div class="card-body">
        <div class="row">

          <div class="col-lg-4 col-md-5 col-sm-4 col-6">
            <div class="nav-tabs-navigation verical-navs">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs flex-column nav-stacked" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active show" href="#info" role="tab" data-toggle="tab" aria-selected="true">Profile & Info</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#description" role="tab" data-toggle="tab" aria-selected="false">Campaign Management</a>
                  </li>
                  <!-- <li class="nav-item">
                    <a class="nav-link" href="#concept" role="tab" data-toggle="tab" aria-selected="false">Concept</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#support" role="tab" data-toggle="tab" aria-selected="false">Support</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#extra" role="tab" data-toggle="tab" aria-selected="false">Extra</a>
                  </li> -->
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-8 col-md-7 col-sm-8 col-6">
            <!-- Tab panes -->
            <div class="tab-content">

              <div class="tab-pane active show" id="info">
                <p>Larger, yet dramatically thinner. More powerful, but remarkably power efficient. With a smooth metal surface that seamlessly meets the new Retina HD display.</p>
                <p>It’s one continuous form where hardware and software function in perfect unison, creating a new generation of phone that’s better by any measure.</p>
              </div>

              <div class="tab-pane" id="description">

                <div class="col-12">
                    <h5>General</h5>
                    <hr />

                    <div class="form-group">
                      <label class="user-settings">Spyder Campaign Structure</label>
                      <input type="checkbox" class="toggle-spyder-campaigns" checked data-toggle="toggle" data-size="small" />
                    </div>

                    <div class="form-group">
                      <label class="user-settings">Bid Adjustment Threshold</label>
                      <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="" aria-label="basic-addon2" aria-describedby="basic-addon2" />
                        <div class="input-group-append">
                          <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                      </div>
                    </div>

                    <h5>Negative Keyword Parameters</h5>
                    <hr />
                    <p>If a search term has 0 sales and falls under any of these parameters, PPCOLOGY will suggest that you add it as a negative keyword.</p>

                    <div class="form-group">
                      <label class="user-settings">Clicks Greater Than</label>
                      <input type="text" class="form-control" placeholder="" />
                    </div>

                    <div class="form-group">
                      <label class="user-settings">CTR Less Than</label>
                      <input type="text" class="form-control" placeholder="" />
                    </div>

                </div>

              </div>

              <!-- <div class="tab-pane" id="concept">
                <p>It’s one continuous form where hardware and software function in perfect unison, creating a new generation of phone that’s better by any measure.</p>
                <p>Larger, yet dramatically thinner. More powerful, but remarkably power efficient. With a smooth metal surface that seamlessly meets the new Retina HD display. </p>
              </div>

              <div class="tab-pane" id="support">
                <p>From the seamless transition of glass and metal to the streamlined profile, every detail was carefully considered to enhance your experience. So while its display is larger, the phone feels just right.</p>
                <p>It’s one continuous form where hardware and software function in perfect unison, creating a new generation of phone that’s better by any measure.</p>
              </div>

              <div class="tab-pane" id="extra">
                <p>It’s one continuous form where hardware and software function in perfect unison, creating a new generation of phone that’s better by any measure.</p>
                <p>Larger, yet dramatically thinner. More powerful, but remarkably power efficient. With a smooth metal surface that seamlessly meets the new Retina HD display. </p>
              </div> -->
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>

  $(function() {

    

 
    
    $("#search-btn").on("click", function() {

      e = $("#keywords").val().split(/\n/).map(function(e) {
        return e.trim()
      }).filter(function(e) {
        return e.length > 0
      });
      
      console.log(e);
      
      var a = e.flatMap(function(e) {
        return [{
          key: e[1],
          matchType: "EXACT"
        }, {
          key: e[1],
          matchType: "BROAD"
        }]
      });

      console.log(a);
      
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


</script>