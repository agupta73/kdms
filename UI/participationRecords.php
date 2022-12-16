    <form id="formAmenity">
        <div class="modal fade" id="ParticipationModalLong" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog kdms-modal" role="document">
                <div class="modal-content">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Accommodation, Seva & Amenity Records</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <?php
                                    $devoteeParicipationSearch = new clsDevoteeSearch($requestData);
                                    $PRResponse = $devoteeParicipationSearch->getParticipationRecords();
                                    unset($devoteeParicipationSearch);
                                    //var_dump($PRResponse);
                                    if (!empty($PRResponse)) {
                                        if (empty($PRResponse['message'])) {
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="text-primary">
                                                <th> Event </th>
                                                <th> Accommodation </th>
                                                <th> Occupied On </th>
                                                <th> Vacated On </th>
                                            </thead>
                                            <tr>
                                                <td colspan="12">
                                                    <div class="scrollbar-dash" id="style-6">
                                                        <table class="table table-striped">
                                                            <?php

                                            foreach ($PRResponse[0] as $PRValue) {
                                                //var_dump($PRValue);
                                                print_r("<td style='width: 150px;font-size: small'>");
                                                print_r(urldecode($PRValue['Event']));
                                                print_r("</td><td style='width: 150px' >");
                                                print_r(urldecode($PRValue['Accommodation']));

                                                print_r("</td><td style='width: 150px' >");
                                                print_r($PRValue['OccupiedOn']);

                                                print_r("</td><td style='width: 150px'>");
                                                print_r($PRValue['VacatedOn']);

                                                /* print_r("</td><td><style='width: 150px'>");
                                                print_r(urldecode($PRValue['Seva']));
                                                print_r("</td><td><style='width: 150px'>");
                                                print_r($PRValue['AssignedOn']);
                                                */

                                                print_r("</td></tr><tr>");
                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="text-primary">
                                                <th> Event </th>
                                                <th> Seva </th>
                                                <th> Attendance </th>
                                            </thead>
                                            <tr>
                                                <td colspan="12">
                                                    <div class="scrollbar-dash" id="style-6">
                                                        <table class="table table-striped">
                                                            <?php

                                            foreach ($PRResponse[1] as $PRValue) {
                                                //var_dump($PRValue);
                                                print_r("<td style='width: 150px;font-size: small'>");
                                                print_r(urldecode($PRValue['Event']));

                                                print_r("</td><td style='width: 150px' >");
                                                print_r(urldecode($PRValue['Seva']));

                                                print_r("</td><td style='width: 200px'>");
                                                print_r($PRValue['Attendance']);


                                                print_r("</td></tr><tr>");
                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="text-primary">
                                                <th> Event </th>
                                                <th> Allocations </th>
                                            </thead>
                                            <tr>
                                                <td colspan="12">
                                                    <div class="scrollbar-dash" id="style-6">
                                                        <table class="table table-striped">
                                                            <?php

                                            foreach ($PRResponse[2] as $PRValue) {
                                                //var_dump($PRValue);
                                                print_r("<td style='width: 200px;font-size: small'>");
                                                print_r(urldecode($PRValue['Event']));

                                                print_r("</td><td style='width: 200px' >");
                                                print_r(urldecode($PRValue['Allocations']));

                                                print_r("</td></tr><tr>");
                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="text-primary">
                                                <th> Event </th>
                                                <th> Remarks </th>
                                            </thead>
                                            <tr>
                                                <td colspan="12">
                                                    <div class="scrollbar-dash" id="style-6">
                                                        <table class="table table-striped">
                                                            <?php

                                            foreach ($PRResponse[3] as $PRValue) {
                                                //var_dump($PRValue);
                                                print_r("<td style='width: 120px;font-size: small'>");
                                                print_r(urldecode($PRValue['Event']));

                                                print_r("</td><td style='width: 280px' >");
                                                print_r(str_replace('||', '<br>', urldecode($PRValue['Remarks'])));

                                                print_r("</td></tr><tr>");
                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
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