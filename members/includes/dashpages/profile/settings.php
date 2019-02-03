<?php


?>

<div class="row">
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
                    <a class="nav-link" href="#general" role="tab" data-toggle="tab" aria-selected="false">General</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#description" role="tab" data-toggle="tab" aria-selected="false">Automation Settings</a>
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
              
              <!-- used for general user settings a.k.a password change, view terms of service, billing cycles, etc-->
              <div class="tab-pane" id="general">
                <div class="settings col-12">
                  <h5><b>User Settings</b></h5>
                  <hr />

                  <div class="form-group">
                    <div class="row">
                      <label class="user-settings col-sm-5">Current Marketplace Name: </label>
                      <label class="user-settings col-sm-4">Placeholder Name </label>
                      <button type="button" class="change-marketplace btn btn-primary col-xs-2">Change</button>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <label class="user-settings col-sm-5">Current Billing Plan:</label>
                      <label class="user-settings col-sm-4">Placeholder Plan</label>
                      <button type="button" class="change-billing btn btn-primary col-xs-2">Change</button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="row">
                      <label class="user-settings col-sm-5">Current Email Address:</label>
                      <label class="user-settings col-sm-4">Placeholder email</label>
                      <button type="button" class="change-email btn btn-primary col-xs-2">Change</button>
                    </div>
                  </div>

                </div>
              </div>

              <div class="tab-pane" id="description">
                <div class="settings col-12">
                  <h5><b>Global Automation Settings</b></h5>
                  <hr />

                  <div class="form-group">
                    <div class="row justify-content-between">
                      <label class="user-settings col-sm-6">Spyder Campaign Structure</label>
                      <input type="checkbox" class="toggle-spyder-campaigns col-sm-4" checked data-toggle="toggle" data-size="medium" />
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row justify-content-between">

                      <label class="user-settings col-sm-6">Bid Adjustment Threshold</label>

                      <div class="input-group col-sm-2">
                        <input type="text" class="form-control input-sm" placeholder="" aria-label="basic-addon2" aria-describedby="basic-addon2" />
                        <div class="input-group-append">
                          <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                      </div>

                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row justify-content-between">

                      <label class="user-settings col-sm-4">Target ACoS</label>
                      <div class="input-group col-sm-2">
                        <input type="text" class="form-control input-sm" placeholder="" aria-label="basic-addon2" aria-describedby="basic-addon2" />
                        <div class="input-group-append">
                          <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                      </div>

                    </div>
                  </div>

                  <h5><b>Negative Keyword Parameters</b></h5>
                  <hr />
                  <p>If a search term has 0 sales and falls under any of these parameters, PPCOLOGY will suggest that you add it as a negative keyword.</p>

                  <div class="form-group">
                    <div class="row justify-content-between">
                      <label class="user-settings col-sm-4">Clicks Greater Than</label>
                      <input type="text" class="form-control input-sm col-sm-2" placeholder="" />
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row justify-content-between">
                      <label class="user-settings col-sm-4">CTR Less Than</label>
                      <input type="text" class="form-control input-sm col-sm-2" placeholder="" />
                    </div>
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
      </div> <!-- card body -->
    </div> <!-- card -->
    <center><a href="#" data-toggle="modal" data-target="#tosModal">View Terms of Service</a></center>

    <div class="modal fade" id="tosModal" tab-index="-1" role="dialog" aria-labelledby="tosLabel" aria-hidden="hidden">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tosLabel">Terms Of Service</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
		  
		  <div class="modal-body">
		    <div class="container">
			  <center>insert tos text here</center>
			</div>
		  </div>
        </div>
      </div>
    </div> <!-- tos modal -->
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
