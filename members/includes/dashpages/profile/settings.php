<?php


?>

<div class="row">
  
  <input type="text" name="keyword" />
  <button id="#search-btn">Get Volume</button>
  
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

<script type="text/javascript" src="/kw_searchVolume.js"></script>