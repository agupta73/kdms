<form id="formAmenity">
    <div class="modal fade" id="ParticipationModalLong" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="width:1200px;height:900px;">

                <div class="card-header card-header-primary">
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="row">
                            <div class="col-lg-12 col-md-9 col-sm-6">
                                <div class="card">
                                    <div class="card-header card-header-primary">
                                        <h4 class="card-title">Accommodation & Seva Records</h4>

                                    </div>
                                    <?php
                                $PRResponse = $devoteeSearch->getParticipationRecord();
                                //var_dump($amenityResponse);die;
                                if (!empty($PRResponse)) {
                                    if (empty($PRResponse['message'])) {
                                ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
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
                                                <td colspan="12">
                                                    <div class="scrollbar-dash" id="style-6">
                                                        <table class="table table-striped">
                                                            <?php
                                        // var_dump($PRResponse);
                                        foreach ($PRResponse as $key => $PRValue) {
                                            print_r("<td style='width: 150px;font-size: small'>");
                                            print_r(urldecode($PRValue['Event']));

                                            print_r("</td><td style='width: 150px' >");
                                            print_r(urldecode($PRValue['Accommodation']));

                                            print_r("</td><td style='width: 150px' >");
                                            print_r($PRValue['OccupiedOn']);

                                            print_r("</td><td><style='width: 150px'>");
                                            print_r($PRValue['VacatedOn']);

                                            print_r("</td><td><style='width: 150px'>");
                                            print_r(urldecode($PRValue['Seva']));

                                            print_r("</td><td><style='width: 150px'>");
                                            print_r($PRValue['AssignedOn']);

                                            print_r("</td>");
                                            if ($key < count($PRResponse)) {
                                                print_r("</tr><tr>");
                                            }
                                        }
                                            ?>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                    } else { ?>
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
                                if ($debug) {
                                    var_dump($PRResponse);
                                    die;
                                }
                                ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>

</form>