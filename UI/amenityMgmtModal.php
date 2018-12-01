<div class="modal fade" id="AmenityModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                  <div class="modal-dialog" role="document" >
                    <div class="modal-content" style="width:800px;height:600px;">
                      
                        <div class="card-header card-header-primary">
                      </div>
                      <div class="modal-body" >
                          <div class="card">
                        <div class="row">
                          <div class="col-md-4">
                              <div class="card-header card-header-primary">
                                    <h4 class="card-title">Allocated Amenities</h4>
                                  </div>
                              <?php 
                                $amenityResponse = $devoteeSearch->getDevoteeAmenities();
                                //var_dump($amenityResponse);die;
                                if(!empty($amenityResponse) ){
                                    if(empty($amenityResponse['message'])){                              
                               ?>
                              <table class="table" style="width: 200px"> 
                                  <thead class=" text-primary">
                                    <th>
                                      Amenity Type
                                    </th>
                                    <th align='right'>
                                        Allocated Quantity
                                    </th>
                                  <tr>
                                  <?php  
                                    foreach ($amenityResponse as $key => $amenityValue) { ?>
                                      <td style="width: 50px">
                                          <?php 
                                            print_r($amenityValue['Amenity_Name'] . ": " );
                                            print_r("</td><td style='width: 50px' align='right'>");
                                            print_r($amenityValue['Amenity_Quantity']);
                                          ?>
                                      </td>                                        
                                      <?php 
                                        if($key < count($amenityResponse)){
                                            print_r("</tr><tr>");
                                        }
                                    }
                                        ?>
                                  </tr>
                              </table>
                              
                                <?php                                 
                                }
                                else { ?>
                              <table>
                                  <tr>
                                      <td>
                                          No amenities allocated yet..
                                      </td>
                                  </tr>
                              </table>      
                              
                               <?php
                               }
                                }
                                ?>
                          </div>
                            <?php 
                            if(!empty($amenities)){
                                  
                            ?>
                          <div class="col-md-8">
                                <div class="card-header card-header-primary">
                                    <h4 class="card-title">Issue/Return Amenities</h4>
                                  </div>
                               <div class="table-responsive">
                              <table class="table table-hover" style="width: 500px"> 
                                  <thead class=" text-primary">
                                    <th>
                                      Amenity Type
                                    </th>
                                    <th align='right'>
                                        Available Quantity
                                    </th>
                                    <th align='right'>
                                        Issue
                                    </th>
                                    <th align='right'>
                                        Return
                                    </th>
                                    </thead>
                                  <?php 
                                        foreach($amenities as $key => $amenityRecord){
                                  
                                    print_r("<tr>");
                                      print_r("<td>");
                                          print_r($amenityRecord['Amenity_Name']);
                                      print_r("</td>");
                                       print_r("<td>");
                                          print_r($amenityRecord['Available_Count']);
                                      print_r("</td>");
                                      print_r("<td>"); 
                                    print_r("<input type='text' id='I" . $amenityRecord['amenity_key'] . "'");
                                      print_r("</td>");                                      
                                       print_r("<td>");
                                          print_r("<input type='text' id='R" . $amenityRecord['amenity_key'] . "'");
                                     print_r("</td>");
                                  print_r("</tr>");
                                         } ?>
                              </table>
                               </div>
                        </div>
                            
                            <?php
                            
                                        
                                        }
                            ?>
                      </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="save-amenity" type="button" class="btn btn-primary">Save Changes</button>
                        <input type="hidden" id="devotee_key_amenity_modal" name="devotee_key_amenity_modal" value="<?php print_r($devotee_key); ?>">
                      </div>
                    </div>
                  </div>
                </div>