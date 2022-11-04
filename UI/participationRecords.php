<form id="formAmenity">
<div class="modal fade" id="ParticipationModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                  <div class="modal-dialog" role="document" >
                    <div class="modal-content" style="width:800px;height:600px;">
                      
                        <div class="card-header card-header-primary">
                      </div>
                      <div class="modal-body" >
                          <div class="card">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="card-header card-header-primary">
                                    <h4 class="card-title">Accommodation & Seva Records</h4>

                                  </div>
                              <?php 
                                $PRResponse = $devoteeSearch->getParticipationRecord();
                                //var_dump($amenityResponse);die;
                                if(!empty($PRResponse) ){
                                    if(empty($PRResponse['message'])){
                               ?>
                             <div class="table-responsive">
                              <table class="table table-hover"  > 
                                  <thead class=" text-primary">
                                    <th>
                                        Event
                                    </th>
                                    <th>
                                        Accommodation
                                    </th>
                                    <th>
                                        Occupied On
                                    </th>
                                    <th>
                                        Vacated On
                                    </th>
                                    <th>
                                        Seva
                                    </th>
                                    <th>
                                        Assigned On
                                    </th>
                                </thead>
                                  <tr>
                                  <?php
                                   // var_dump($PRResponse);
                                    foreach ($PRResponse as $key => $PRValue) {
                                            print_r("<td style='width: 150px;font-weight: bold;font-size: medium'>");
                                            print_r($PRValue['Event']  );

                                            print_r("</td><td style='width: 150px' align='right'>");
                                            print_r($PRValue['Accommodation']);

                                            print_r("</td><td style='width: 150px' align='right'>");
                                            print_r($PRValue['OccupiedOn']);

                                            print_r("</td><td><style='width: 150px' align='right'>");
                                            print_r($PRValue['VacatedOn'] . "'");

                                            print_r("</td><td><style='width: 150px' align='right'>");
                                            print_r($PRValue['Seva'] . "'");

                                            print_r("</td><td><style='width: 150px' align='right'>");
                                            print_r($PRValue['AssignedOn'] . "'");

                                            print_r("</td>");
                                            if($key < count($PRResponse)){ print_r("</tr><tr>"); }
                                        }
                                    ?>
                                  </tr>
                              </table>
                             </div>
                                <?php                                 
                                }
                                else { ?>
                              <table>
                                  <tr>
                                      <td style="align-content: center">
                                          No Participation so far..
                                      </td>
                                  </tr>
                              </table>      
                              
                               <?php
                               }
                                }
                               if($debug){var_dump($PRResponse);die;}
                                ?>
                          </div>

                      </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="save-amenity" type="button" class="btn btn-primary" onclick="saveAmenityData('formAmenity'); return false;">Save Changes</button>
                        <input type="hidden" id="devotee_key" name="devotee_key" value="<?php print_r($devotee_key); ?>">
                        <input type="hidden" id="requestType" name="requestType" value="manageAmenity">
                        <input type="hidden" id="amenity_key" name="amenity_key">
                        <input type="hidden" id="amenity_quantity" name="amenity_quantity">

                      </div>
                    </div>
                  </div>
                </div>

</form>
<script>

//javascript function for ajax call
  function saveAmenityData(formId){
    
    if(validateAmenity(formId)){ 
        var strKey="";
        var strVal="";
        var updateSuccess = true;
        
            for(i=0; i < document.getElementById(formId).length; i++){
                
                if(document.getElementById(formId)[i].type == "text"){
                
                if(document.getElementById(formId)[i].id.substring(0,1) == "I" && document.getElementById(formId)[i].value.trim().length != 0){
                    strKey = strKey + document.getElementById(formId)[i].id.substring(1,document.getElementById(formId)[i].id.length) + ",";
                    strVal = strVal + document.getElementById(formId)[i].value.trim() + ",";
                }
                else if (document.getElementById(formId)[i].id.substring(0,1) == "R" && document.getElementById(formId)[i].value.trim().length != 0){
                    strKey = strKey + document.getElementById(formId)[i].id.substring(1,document.getElementById(formId)[i].id.length) + ",";
                    strVal = strVal + "-" + document.getElementById(formId)[i].value.trim() + ",";
                }
            }
        }
 
            var formData = {'devotee_key': document.getElementById("devotee_key").value, 
                        'amenity_key': strKey.substring(0,strKey.length -1),
                        'amenity_quantity': strVal.substring(0,strVal.length -1),
                        'requestType': "manageAmenity",
                            };
        
         $.ajax({
            url:'/KDMS/Logic/requestManager.php',
            type:'POST',
            data:formData,
            async: false,
            success:function(response){
                  var r = JSON.parse(response);
                  //alert(response);
                  if(r['flag'] == true){                      
                      alert("Amenity successfully updated!");
                      $('#AmenityModalLong').modal('hide');
                  }
                  else{
                      alert(r['message']);
                      updateSuccess=false;
                  }   
            }
          });
    }
  }
  
function validateAmenity(formId){
    var valueEntered = false;
    var valueNonNumber = false;
    
    for(i=0; i<document.getElementById(formId).length; i++){
        if(document.getElementById(formId)[i].type == "text"){
            if(document.getElementById(formId)[i].value.trim().length != 0){
                //alert(document.getElementById(formId)[i].id.substring(1,document.getElementById(formId)[i].id.length));
                valueEntered = true;
                valueNonNumber = isNaN(document.getElementById(formId)[i].value.trim());
            }            
        }
    }
    if(!valueEntered || valueNonNumber){
        alert("Please enter number to issue or return amenity.");
        return false;
    } else {    
        return true;
    }
}
    
    
</script>