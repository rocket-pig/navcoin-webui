<?php
include ("header.php");
include ("pass.php");

        $communityfundinfos = $coin->cfundstats();

        //'votelist' is a nested list of lists of proposals grouped by our vote, like [['yes'],['no'],['null']].
        //this flattens that into something more like [proposals].
        //(adds 'myvote' to each instead of having three arrays.)
        $proposalvotelist = $coin->proposalvotelist();
        $pv = array($proposalvotelist);
        $pvl = $pv[0];
        foreach ($pvl['yes'] as $key => $val) {
            $pvl['yes'][$key]['myvote'] = 'yes';

        }
        foreach ($pvl['no'] as $key => $value) {
            $pvl['no'][$key]['myvote'] = 'no';
        }
        foreach ($pvl['null'] as $proposal => $val) {
            $pvl['null'][$key]['myvote'] = '';
        }

        $allProposalVotes = array_merge(array_merge($pvl['yes'],$pvl['no']),$pvl['null']);
        //debug, this will literally dump array into page.
        //print_r($allProposalVotes);

        //do the same thing again for paymentrequest list.
        //todo: could combine logic into a function and call it twice.
        $paymentrequestvotelist = $coin->paymentrequestvotelist();
        $pv = array($paymentrequestvotelist);
        $pvl = $pv[0];
        foreach ($pvl['yes'] as $key => $val) {
            $pvl['yes'][$key]['myvote'] = 'yes';

        }
        foreach ($pvl['no'] as $key => $value) {
            $pvl['no'][$key]['myvote'] = 'no';
        }
        foreach ($pvl['null'] as $proposal => $val) {
            $pvl['null'][$key]['myvote'] = '';
        }

        $allPaymentVotes = array_merge(array_merge($pvl['yes'],$pvl['no']),$pvl['null']);

        //debug, this will literally dump array into page.
        //print_r($allPaymentVotes);

?>


<p><b>Community Fund:</b></p>
<p>Current Proposals</p>
<!-- beginning to new stuff. -->
<?php
$count = 0; //used to create two columns per row.
echo "<div class='row'>";
foreach ($allProposalVotes as $row) {
    //notPaidYet userPaidFee paymentAddress proposalDuration
    $desc = $row['description'];
    $votesYes = $row['votesYes'];
    $votesNo = $row['votesNo'];
    $votingCycle = $row['votingCycle'];
    //status = 'pending'
    $status = $row['status'];
    $amount = $row['requestedAmount'];
    $notPaidYet = $row['notPaidYet'];
    $hash = $row['hash'];
    $duration = $row['proposalDuration'];
    $yourVote = $row['myvote'] ?? 'please vote!'; // ?? is 'or default:'
    $navPaid = $amount - $notPaidYet;
    $valueMax = $votesYes + $votesNo;
    //'division by zero' warnings in log are to be avoided:
    $percentYes = 0; $percentNo = 0;
    if ($valueMax > 0){
      $percentYes = ($votesYes / $valueMax)*100;
      $percentNo = ($votesNo / $valueMax)*100;
    };
    if ($yourVote === 'yes'){$voteColor = 'green';};
    if ($yourVote === 'no'){$voteColor = 'red';};
    if ($yourVote === 'please vote!'){$voteColor = 'orange';};

    //create/split columns:
    if ($count >= 3) {
      $count = 0;
      echo "</div>
            <div class='row'>";
    };

    echo "
        <div class='col-sm-4' style='border-left: 1px solid black; border-top: 1px solid black;'>

        <div class='card text-dark bg-light'>
        <div style='background-color:gray; color:white;' class='card-header bg-dark text-center text-light'><h4>{$hash}</h4></div>
          <div class='card-body '>
            <h5 class='card-title'>{$desc}</h5>
            <!--Starting list group here -->
                <div class='list-group'>

                <a style='background-color: white;' class='list-group-item d-flex justify-content-between align-items-center text-light bg-dark'>Amount
                  <span class='badge badge-primary badge-pill'>{$amount} NAV</span>
                </a>

                  <a style='background-color: white;' class='list-group-item d-flex justify-content-between align-items-center text-light'>Yes
                  <span class='badge badge-primary badge-pill'>{$votesYes}</span>
                  </a>
                  <div class = 'progress'>
                      <div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' style='background-color: green; width: {$percentYes}&percnt;' aria-valuenow='{$percentYes}' aria-valuemin='0' aria-valuemax='100'>
                  </div></div>


                  <a style='background-color: white;' class='list-group-item d-flex justify-content-between align-items-center text-light'>No
                  <span class='badge badge-primary badge-pill'>{$votesNo}</span>
                  </a>
                  <div class = 'progress'>
                      <div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' style='background-color: red; width: {$percentNo}&percnt;' aria-valuenow='{$percentNo}' aria-valuemin='0' aria-valuemax='100'>
                  </div></div>

                  <a style='background-color: white;' class='list-group-item d-flex justify-content-between align-items-center text-light bg-dark'>
                    Voting Cycle
                    <span class='badge badge-primary badge-pill'>{$votingCycle}</span>
                  </a>
                  <a style='background-color: white;' class='list-group-item d-flex justify-content-between align-items-center'>Status
                  <span class='badge badge-primary badge-pill'>{$status}</span>
                  </a>
                  <a style='background-color: white;' class='list-group-item d-flex justify-content-between align-items-center'>My Vote
                  <span style='background-color: {$voteColor};' class='badge badge-primary badge-pill'>{$yourVote}</span>
                  </a>
                </div>
              <!--Ends here -->
          </div>
          <div class='card-footer bg-secondary border-danger text-right'>
            <form action='command' method='POST'>
              <button style='float:left' class='btn btn-success btn-sm' type='submit' value='command'>Vote 'Yes'</button>
              <input type='hidden' name='order' value='proposalvote' />
              <input type='hidden' name='var1' value='{$hash}' />
              <input type='hidden' name='var2' value='yes' />
            </form>
            <form action='command' method='POST'>
              <button style='float:left' class='btn btn-danger btn-sm' type='submit' value='command'>Vote 'No'</button>
              <input type='hidden' name='order' value='proposalvote' />
              <input type='hidden' name='var1' value='{$hash}' />
              <input type='hidden' name='var2' value='no' />
            </form>

          <!-- <a href='#' class='btn btn-info btn-sm'>Tutorials</a> <a href='#' class='btn btn-success btn-sm'>Demos</a> -->

          </div>
         </div>
        </div>";
        $count++; //advance in row placement count.
}
?>
</div>
<!-- end of new stuff -->

<p>Current Payment Requests</p>
<div class="panel panel-default">
  <div class="table-responsive">
    <?php
      echo "<table class='table-hover table-condensed table-bordered table'>
              <thead>
                <tr>
                  <th>Payment Request Description</th>
                  <th>Amount</th>
                  <th>Your vote</th>
                  <th>Vote</th>
                </tr>";

                // at the moment, paymentrequestvotelist() returns a string which would have to be parsed
                // it's easier to loop over the result of cfundstats until paymentrequestvotelist returns a json object
                // with the json, we could loop over the yes-votes, no-votes, non-votes separately and don't have to check them
                //foreach ($communityfundinfos['votingPeriod']['votedPaymentrequest'] as $row) {
                foreach ($allPaymentVotes as $row) {
                  $proposalDesc = $row['description'];
                  //$paymentRequestDesc = $row['desc'];
                  $amount = $row['requestedAmount'];
                  $hash = $row['hash'];
                  $yourVote = $row['myvote'];

                  echo "<tr>
                          <td class='valignmiddle' style='width:70%'>{$proposalDesc}</td>
                          <td class='valignmiddle'>{$amount}</td>
                          <td class='valignmiddle'>{$yourVote}</td>
                          <td class='valignmiddle'>
                            <div class='input-group'>
                              <span class='input-group-btn'>
                                <form action='command' method='POST'><input type='hidden'>
                                  <button style='float:left' class='btn btn-default' type='submit' value='command'>Yes</button>
                                  <input type='hidden' name='order' value='paymentrequestvote' />
                                  <input type='hidden' name='var1' value='{$hash}' />
                                  <input type='hidden' name='var2' value='yes' />
                                </form>

                                <form action='command' method='POST'>
                                  <button class='btn btn-default' type='submit' value='command'>No</button>
                                  <input type='hidden' name='order' value='paymentrequestvote' />
                                  <input type='hidden' name='var1' value='{$hash}' />
                                  <input type='hidden' name='var2' value='no' />
                                </form>
                              </span>
                            </div>
                          </td>
                        </tr>";
                  } //endforeach
                  echo "</table>";
      ?>
    </div>
  </div>
</div>
<?php include ("footer.php"); ?>
