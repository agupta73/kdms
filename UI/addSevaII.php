<?php

declare(strict_types=1);

/**
 * Canonical page: Manage Seva Types listing (KD-SEVA-II).
 */

require_once dirname(__DIR__) . '/includes/web_session.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Seva II)
  </title>
  <?php

  include_once 'header.php';
  include_once '../Logic/clsOptionHandler.php';

  ?>
</head>

<body class="">

  <div class="wrapper ">
    <?php

    include_once 'nav.php';
    $debug = false;
    $sevaSearch = new clsOptionHandler('Seva');
    $sevaSearch->setOptionKey('');
    $sevaSearch->setEventId($config_data['event_id']);
    $response = $sevaSearch->getOptions();

    if ($debug) {
        var_dump($response);
        echo 'reaching here..';
        exit;
    }

    unset($sevaSearch);
    ?>

    <div class="main-panel">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Seva records</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover" >
                    <thead class=" text-primary">
                      <th>
                        Seva ID
                      </th>
                      <th>
                        Description
                      </th>
                      <th align='right'>
                        Assigned
                      </th>
                    </thead>
                    <tbody>
                      <tr>
                        <td colspan="12">
                          <div class="scrollbar-dash" id="style-6">
                            <table class="table table-striped">
                              <?php
                              $rows = (is_array($response) && array_key_exists(0, $response))
                                  ? $response
                                  : [];
                              foreach ($rows as $sevaRecord) {
                                  if (! is_array($sevaRecord)) {
                                      continue;
                                  }
                                  $sevaId = $sevaRecord['Seva_Id'] ?? $sevaRecord['seva_id'] ?? '';
                                  $desc = $sevaRecord['Seva_Description'] ?? $sevaRecord['seva_description'] ?? '';
                                  $assigned = $sevaRecord['assigned_count'] ?? '--';
                                  $sevaId = is_string($sevaId) ? urldecode($sevaId) : (string) $sevaId;
                                  $desc = is_string($desc) ? urldecode($desc) : (string) $desc;
                                  if ($sevaId === '') {
                                      continue;
                                  }
                                  $qSeva = rawurlencode($sevaId);
                                  ?>
                              <tr>
                                <td>
                                  <a href="addSevaI.php?seva_id=<?= htmlspecialchars($qSeva, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($sevaId, ENT_QUOTES, 'UTF-8') ?></a>
                                </td>
                                <td align="left">
                                  <a href="addSevaI.php?seva_id=<?= htmlspecialchars($qSeva, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></a>
                                </td>
                                <td align="right">
                                  <?= htmlspecialchars((string) $assigned, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                              </tr>
                                  <?php
                              }
                              ?>
                            </table>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="col-md-12">
              <form action="addSevaI.php">
                <button type="submit" class="btn btn-success pull-right">Add new seva</button>
                <div class="clearfix"></div>
              </form>
            </div>
          </div>

        </div>


      </div>
    </div>
  </div>

  <!-- id modial -->

  </div>
  <?php
  include_once 'scriptJS.php' ?>
</body>

</html>
