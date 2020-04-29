<?php
   // Initialize the session
   session_start();
   // Check if the user is logged in, if not then redirect him to login page
   if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
      header("location: login.php");
      exit;
   }
   //displays your username at the top of page
   if($_SESSION['loggedin']==true){ 
         echo "Logged in as ". $_SESSION["username"];
   }
   $currentUserName = $_SESSION["username"];
   $HOST = 'tethys.cse.buffalo.edu';
   $USERNAME = 'jling2';
   $USERPASSWORD = "50244515";
   $DBNAME = "cse442_542_2020_spring_teaml_db";

   $conn = new mysqli($HOST, $USERNAME, $USERPASSWORD, $DBNAME);

   $sql = "SELECT wins, losses FROM users WHERE username = '$currentUserName' ";
   $result = $conn->query($sql);

   //displays your game stats i.e. wins and losses
   if ($result->num_rows > 0) {
      // output data of each row
      while (($row = $result->fetch_assoc())) {
         echo "<br> Current Stats: Wins: ". $row["wins"].  " Losses: ". $row["losses"].  "<br>";
      } 
   }
   
?>
 
<!DOCTYPE html>
<html>

<a href="logout.php" class="btn btn-danger">Log Out</a><!--logout button-->

<head>
  
</head>
<body>


<H1>Neck 4 - Local Multiplayer Mode</H1>

<!--links to css file-->
<link rel="stylesheet" href="style.css">

<div id="colorTurn">Yellow Turn (Thats You)</div>
<div id="board">
<div class="row">
  <div class="cell" id="cell00" onclick="selectColumn(0)"></div>
  <div class="cell" id="cell01" onclick="selectColumn(1)"></div>
  <div class="cell" id="cell02" onclick="selectColumn(2)"></div>
  <div class="cell" id="cell03" onclick="selectColumn(3)"></div>
  <div class="cell" id="cell04" onclick="selectColumn(4)"></div>
  <div class="cell" id="cell05" onclick="selectColumn(5)"></div>
  <div class="cell" id="cell06" onclick="selectColumn(6)"></div>
</div>
<div class="row">  
  <div class="cell" id="cell10" onclick="selectColumn(0)"></div>
  <div class="cell" id="cell11" onclick="selectColumn(1)"></div>
  <div class="cell" id="cell12" onclick="selectColumn(2)"></div>
  <div class="cell" id="cell13" onclick="selectColumn(3)"></div>
  <div class="cell" id="cell14" onclick="selectColumn(4)"></div>
  <div class="cell" id="cell15" onclick="selectColumn(5)"></div>
  <div class="cell" id="cell16" onclick="selectColumn(6)"></div>
</div>
<div class="row">  
  <div class="cell" id="cell20" onclick="selectColumn(0)"></div>
  <div class="cell" id="cell21" onclick="selectColumn(1)"></div>
  <div class="cell" id="cell22" onclick="selectColumn(2)"></div>
  <div class="cell" id="cell23" onclick="selectColumn(3)"></div>
  <div class="cell" id="cell24" onclick="selectColumn(4)"></div>
  <div class="cell" id="cell25" onclick="selectColumn(5)"></div>
  <div class="cell" id="cell26" onclick="selectColumn(6)"></div>
</div>
<div class="row">  
  <div class="cell" id="cell30" onclick="selectColumn(0)"></div>
  <div class="cell" id="cell31" onclick="selectColumn(1)"></div>
  <div class="cell" id="cell32" onclick="selectColumn(2)"></div>
  <div class="cell" id="cell33" onclick="selectColumn(3)"></div>
  <div class="cell" id="cell34" onclick="selectColumn(4)"></div>
  <div class="cell" id="cell35" onclick="selectColumn(5)"></div>
  <div class="cell" id="cell36" onclick="selectColumn(6)"></div>
</div>
<div class="row">
  <div class="cell" id="cell40" onclick="selectColumn(0)"></div>
  <div class="cell" id="cell41" onclick="selectColumn(1)"></div>
  <div class="cell" id="cell42" onclick="selectColumn(2)"></div>
  <div class="cell" id="cell43" onclick="selectColumn(3)"></div>
  <div class="cell" id="cell44" onclick="selectColumn(4)"></div>
  <div class="cell" id="cell45" onclick="selectColumn(5)"></div>
  <div class="cell" id="cell46" onclick="selectColumn(6)"></div>
</div>
<div class="row">
  <div class="cell" id="cell50" onclick="selectColumn(0)"></div>
  <div class="cell" id="cell51" onclick="selectColumn(1)"></div>
  <div class="cell" id="cell52" onclick="selectColumn(2)"></div>
  <div class="cell" id="cell53" onclick="selectColumn(3)"></div>
  <div class="cell" id="cell54" onclick="selectColumn(4)"></div>
  <div class="cell" id="cell55" onclick="selectColumn(5)"></div>
  <div class="cell" id="cell56" onclick="selectColumn(6)"></div>
</div>
</div>
<input id="resetButton" type="button" value="Undo" onclick="undoMove()" /></br></br><!--button to undo a move-->
<input id="resetButton" type="button" value="Clear/Start New Game" onclick="clearBoard()" /></br></br><!--resets the board button-->
<input id="resetButton" type="button" value="Make Bot Move" onclick="makeBotMove()" /></br></br><!--resets the board button-->
<a href="createOnlineGame.php" class="btn btn-danger">Play online</a>



<!--*************************************JAVASCRIPT***********Start**********************************************-->
<script>

const COLS = 7;
const ROWS = 6;
const chainSize = 4// default is 4 consecutive pieces to win

var board = [];
var turn = 1; //1 for Yellow, 2 for Red
var win = false;

//saves all moves into a "stack"    -   this is for the undo button
//ex. if p1 makes a move at positions board[1][2] then [1,2] will be pushed to the stack
var moveHistory = [];

/*
INITIALIZE A NEW BOARD
should look like this
var board = [
   [0, 0, 0, 0, 0, 0, 0],
   [0, 0, 0, 0, 0, 0, 0],
   [0, 0, 0, 0, 0, 0, 0],
   [0, 0, 0, 0, 0, 0, 0],
   [0, 0, 0, 0, 0, 0, 0],
   [0, 0, 0, 0, 0, 0, 0]
 ];*/ 
function newBoard(board){
   for(x = 0; x < ROWS; x++){
      board[x] = []
      for(y = 0; y < COLS; y++){
         board[x][y] = 0;
      }
   }
}
newBoard(board);

//read the board from the table on tethys. Its stored there as 6 strings each one corresponds to a row
function readFromDatabase(){

}

//writes the board onto the database
function writetoDatabase(){
   //convert each row into strings
   var row0 = board[0].join('');
   var row1 = board[1].join('');
   var row2 = board[2].join('');
   var row3 = board[3].join('');
   var row4 = board[4].join('');
   var row5 = board[5].join('');

   //puts the strings into cookies so the variables can be accessed using php
   document.cookie = "row0="+row0;
   document.cookie = "row1="+row1;
   document.cookie = "row2="+row2;
   document.cookie = "row3="+row3;
   document.cookie = "row4="+row4;
   document.cookie = "row5="+row5;

   <?php
      //retrieves the data from the cookies
      $row0 = $_COOKIE["row0"];
      $row1 = $_COOKIE["row1"];
      $row2 = $_COOKIE["row2"];
      $row3 = $_COOKIE["row3"];
      $row4 = $_COOKIE["row4"];
      $row5 = $_COOKIE["row5"];

      $result = $conn->query("SELECT * FROM `SavedOfflineGames` WHERE `username` = '$currentUserName' ");//checks if players saved game is there or not

      if ($result->num_rows == 0) {//if not there create a new entry
         $sql = "INSERT INTO `SavedOfflineGames` (username, row0, row1, row2, row3, row4, row5) 
                                    VALUES ('$currentUserName', '$row0', '$row1','$row2','$row3','$row4','$row5')";
         $conn->query($sql);   
      }else{//if its there it will update the existing one
         $sql = "UPDATE `SavedOfflineGames` SET row0 = '$row0', row1 = '$row1', row2 = '$row2', row3 = '$row3', row4 = '$row4', row5 = '$row5' 
                                          WHERE username = '$currentUserName'" ;                          
         $conn->query($sql);   
      }
   ?>
   

}

//this add a game piece to a column and does some other stuff
function selectColumn(col) {
   if(!win){
      if (turn==1) {
         var row = board.length - 1;
         //columns 5 to 0 (default)
         while (row > -1) { 
            if(board[row][col] != 0 ){//if the slot is taken then go up a row
               row--;
            }else{//otherwise the pieces is placed here
               board[row][col]=1;
               pushToMoveHistory(row,col);//move is pushed into the move history stack
               break;
            }
         }
         turn=2;//go to next players turn (red)
         document.getElementById("colorTurn").innerHTML="Red Turn";//changes the on top of board to display red players turn
         
      } else {
         var row = board.length - 1;
         while (row > -1) { 
            if(board[row][col] !=0 ){
               row--;
            }else{
               board[row][col]=2;
               pushToMoveHistory(row,col);
               break;
            }
         }
         turn=1;
         document.getElementById("colorTurn").innerHTML="Yellow Turn";//changes the on top of board to display yellow players turn 
      }
      updateBoard();//updates the display for the board
      writetoDatabase();//updates the board to the database
      
      //checks if player1/yellow won
      if(determineWin(board) == 1){
         document.getElementById("colorTurn").innerHTML="Yellow/You Win!";
         win = true;
         <?php
            $sql = "UPDATE users SET wins = wins + 1 WHERE  username = '$currentUserName' ";//updates your win (increments it by 1)
            $sql2 = "INSERT INTO `MatchHistory` (player1, player2, win) VALUES ('$currentUserName', 'Player 2', 1)";//updates match history table: 1 means win 0 means lose 
            $conn->query($sql);
            $conn->query($sql2);
         ?>
      //checks if player2/red won   
      }if(determineWin(board) == 2){
         document.getElementById("colorTurn").innerHTML="Red Wins!";
         win = true;
         <?php
            $sql2 = "INSERT INTO `MatchHistory` (player1, player2, win) VALUES ('$currentUserName', 'Player 2', 0)";//you lost lol
            $conn->query($sql2);
         ?>
      }
   }
   
}

//refreshes the connect 4 board after each turn
function updateBoard() {
  for (var row = 0; row < 6; row++) {
    for (var col = 0; col < 7; col++) {
      if (board[row][col]==0) { 
                document.getElementById("cell"+row+col).style.backgroundColor="#FFFFFF";
      } else if (board[row][col]==1) { //1 for yellow
                document.getElementById("cell"+row+col).style.backgroundColor="#FFFF00";
      } else if (board[row][col]==2) { //1 for yellow
                document.getElementById("cell"+row+col).style.backgroundColor="#FF0000";
       }
    }
  }  
}

//HELPERS to check win conditions
function checkRows(matrix){//check horizontal ex. [0 0 0 1 1 1 1]
   for (var row = 0; row < matrix.length; row++){
       for (var col = 0; col < matrix[row].length - 3; col++){
           var element = matrix[row][col];
           if (element == matrix[row][col + 1] && 
               element == matrix[row][col + 2] && 
               element == matrix[row][col + 3] &&
               element == 1){
               return 1;
           }if (element == matrix[row][col + 1] && 
               element == matrix[row][col + 2] && 
               element == matrix[row][col + 3] &&
               element == 2){
               return 2;
           }
       }
   }
   return 0;
}
function checkColumns(matrix){//check vertical
   for (var row = 0; row < matrix.length - 3; row++){
       for (var col = 0; col < matrix[row].length; col++){
           var element = matrix[row][col];
           if (element == matrix[row + 1][col] && 
               element == matrix[row + 2][col] && 
               element == matrix[row + 3][col] &&
               element == 1){
               return 1;
           }if (element == matrix[row + 1][col] && 
               element == matrix[row + 2][col] && 
               element == matrix[row + 3][col] &&
               element == 2){
               return 2;
           }
       }
   }
   return 0;
}
function checkMainDiagonal(matrix){//checks for a positive slope diagonal
   for (var row = 0; row < matrix.length - 3; row++){
       for (var col = 0; col < matrix[row].length - 3; col++){
           var element = matrix[row][col];
           if (element == matrix[row + 1][col + 1] && 
               element == matrix[row + 2][col + 2] && 
               element == matrix[row + 3][col + 3] &&
               element == 1){
               return 1;
           }if (element == matrix[row + 1][col + 1] && 
               element == matrix[row + 2][col + 2] && 
               element == matrix[row + 3][col + 3] &&
               element == 2){
               return 2;
           }
       }
   }
   return 0;
}
function checkCounterDiagonal(matrix){//checks for a negative slope diagonal
   for (var row = 0; row < matrix.length - 3; row++){
       for (var col = 3; col < matrix[row].length; col++){
           var element = matrix[row][col];
           if (element == matrix[row + 1][col - 1] && 
               element == matrix[row + 2][col - 2] && 
               element == matrix[row + 3][col - 3] &&
               element == 1){
               return 1;
           }if (element == matrix[row + 1][col - 1] && 
               element == matrix[row + 2][col - 2] && 
               element == matrix[row + 3][col - 3] &&
               element == 2){
               return 2;
           }
       }
   }
   return 0;
}
//CALLS ALL HELPERS ABOVE to determine if there is win
//will return 0, 1, 2       0 means no one won just yet
//takes in the board matrix as parameter
function determineWin(matrix){
   return  checkRows(matrix) + checkColumns(matrix) + checkMainDiagonal(matrix) + checkCounterDiagonal(matrix);
}
 

//pushes a move into the move history stack
function pushToMoveHistory(row,col){
   moveHistory.push([row,col]);
}
//removes the last piece that was placed
function undoMove() {
   if(!win){//undo only works when nobody has won
      var top = moveHistory[moveHistory.length -1];//gets the "top" of the stack
      board[top[0]][top[1]] = 0; //removes that piece from the board
      moveHistory.pop();//pops the top
      updateBoard();//updates the display
      writetoDatabase();//updates the database
   }
   
}

//resets the board
function clearBoard() {
   //all values back to 0
   for(x = 0; x < ROWS; x++){
      for(y = 0; y < COLS; y++)
         board[x][y] = 0;
   }
   win = false;// nobody won
   turn = 1;// current turn is now player 1
   document.getElementById("colorTurn").innerHTML="Yellow/your Turn";//changes the on top of board to display yellow players turn 
   updateBoard();
   writetoDatabase();//updates the database
}

function makeBotMove() {
    var best_move = 0;
    var best_move_score = -10000000;
    

    for(var col = 0; col < 7; col++){
        var row = 5;
        while(row >= 0 && board[row][col] != 0){
            row = row - 1;
        }
        if(row >= 0){
            board[row][col] = 2;
            var new_score = valueBoardState(board);
            console.log(col+" "+new_score);
            if(new_score > best_move_score){
                best_move = col;
                best_move_score = new_score;
            }
            board[row][col] = 0;
        } 
    }
    selectColumn(best_move);
    return best_move;
}

//this is a medium difficulty bot
//players can still easily win with diagonals but
//the bot will not allow you to win with vertical or horizontal
function valueBoardState(cur_board) {
   //0 = empty 1 = human 2 = bot
   var score = 0;

   //this is so we don't count pieces twice
   //0 = new piece 1 = seen piece
   seen = []
   newBoard(seen);
   
   for(x = 0; x < ROWS; x++){
      for(y = 0; y < COLS; y++){
         if(cur_board[x][y] == 1 && seen[x][y] == 0){
            //this is a human piece so this is negative score
            //we first check the chain in the vertical direction
            //note we have -2 here and +1 in the next portion
            //so our bot slightly prefers blocking compared to attacking
            v_score = -2;
            vertical = x;

            while(vertical >=0 && cur_board[vertical][y] == 1){
               v_score = v_score * 10;
               vertical = vertical - 1;
            }
            //now we say if this chain is closed off by the wall or our piece
            //the points are not added to the score
            if(vertical < 0 || cur_board[vertical][y] == 2){
               score = score; // do nothing
            }
            else{
               score = score + v_score;
            }

            //next horizontal direction
            h_score = -2;
            horizontal = y;

            while(horizontal < COLS && cur_board[x][horizontal] == 1){
               h_score = h_score * 10;
               horizontal = horizontal + 1;
            }
            if((horizontal == COLS || cur_board[x][horizontal] == 2)&&(y - 1 < 0 || cur_board[x][y - 1] == 2)){
               score = score;
            }
            //if 1 side is blocked off we add half the points
            else if((horizontal == COLS || cur_board[x][horizontal] == 2) || (y - 1 < 0 || cur_board[x][y - 1] == 2)){
               h_score = h_score / 2;
               score = score + h_score;
            }
            else{
                score = score + h_score;
            }

            
         }

      }
         
   }
   
   return score;
}
</script>
<!--*************************************JAVASCRIPT***********END**********************************************-->




<!--Every thing in this p tag: Displays a bunch of stuff like leaderboard and player list-->
<p>
   <div id="player_list" class="box">Player List
      <?php
         $currentUserName = $_SESSION["username"];//session is a global variable for current username
         $conn = new mysqli($HOST, $USERNAME, $USERPASSWORD, $DBNAME);
         $sql = "SELECT username FROM users";
         $result = $conn->query($sql);

         if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
               echo "<br>". $row["username"].  "<br>";
            }
         }else {
            echo "<br> 0 results";
         }
         
      ?>
   </div>

   <div id="friends_list" class="box">Friends List
      <?php
         $conn = new mysqli($HOST, $USERNAME, $USERPASSWORD, $DBNAME);
         $sql = "SELECT * FROM `Friends` WHERE `friend1Username` = '$currentUserName'  OR `friend2Username` = '$currentUserName' ";
         $result = $conn->query($sql);

         if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
               if ($row["friend1Username"] === $currentUserName){
                  echo "<br>". $row["friend2Username"].  "<br>";
               }else{
                  echo "<br>". $row["friend1Username"].  "<br>";
               }
            }
         } else {
            echo "<br> 0 results";
         }
  
      ?>
   </div>

   <div id="leaderboard" class="box">Leaderboard
      <?php
         $conn = new mysqli($HOST, $USERNAME, $USERPASSWORD, $DBNAME);
         $sql = "SELECT username, wins, losses FROM users ORDER BY wins DESC";
         $result = $conn->query($sql);
         if ($result->num_rows > 0) {
            // output data of each row
            $counter = 0;
            while (($row = $result->fetch_assoc()) AND ($counter < 10)) {
               echo "<br>". $row["username"]. "<br> Wins: ". $row["wins"].  "   Losses: ". $row["losses"].  "<br>";
               $counter++;
            } 
               
         }
      

      ?>
   </div>

   <div id="match_history" class="box"> Match History
      <?php
         $currentUserName = $_SESSION["username"];
         $conn = new mysqli($HOST, $USERNAME, $USERPASSWORD, $DBNAME);
         $sql = "SELECT * FROM `MatchHistory` WHERE `player1` = '$currentUserName' ";
         $result = $conn->query($sql);
         $outcome = "";
         if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
               if ($row["win"]  == 1) {
                  $outcome = "won.";
               } else {
                  $outcome = "lost.";
               }
               echo "<br>" . $row["player1"]. " played against ". $row["player2"].  " and ". $outcome.  "<br>";
            }  
         }
    

      ?>
   </div>
   <div id="notifications" class="box"> Notifications
      <?php
         $currentUserName = $_SESSION["username"];

         $conn = new mysqli($HOST, $USERNAME, $USERPASSWORD, $DBNAME);

         //search through FriendRequests table for current username
         $result = $conn->query("SELECT * FROM `FriendRequests` WHERE `requester` = '$currentUserName' ");//
         $result2 = $conn->query("SELECT * FROM `FriendRequests` WHERE `requestee` = '$currentUserName' ");//

         $result3 = $conn->query("SELECT * FROM `GameInvites` WHERE `inviter` = '$currentUserName' ");//
         $result4 = $conn->query("SELECT * FROM `GameInvites` WHERE `invitee` = '$currentUserName' ");//

         /*initialized to be empty
         $cantFindUserError = "";*/

         if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
               echo "<br>Your friend request to " . $row["requestee"].  " is pending <br>";
            } 
         }
         if($result2->num_rows > 0){
            while ($row = $result2->fetch_assoc()) {
               echo "<br>You have a friend request from " . $row["requester"].  "<br>";
            }   
         }
         if($result3->num_rows > 0){
            while ($row = $result3->fetch_assoc() ) {
               echo "<br>Your invitation to " . $row["inviter"].  " is pending <br>";
            } 
         }
         if($result4->num_rows > 0){
            while ($row = $result4->fetch_assoc() ) {
               echo "<br>You have an invitation from " . $row["invitee"].  "<br>";
            }          
         }
         $conn->close();

      ?>
   </div>


   <h3>Send Friend Request </h3>
      <form action = "friendsAndInvites\friendRequest.php" method= "post">
         <b>Type their username:</b> <input type = "text" name = "user_name">
         <input type = "submit" value="Send">
      </form>
      <!--<span class="help-block"><?php //echo $username_err; ?></span>-->
   <h3>Invite Friend to Game</h3>
      <form action = "friendsAndInvites\inviteFriend.php" method= "post">
         <b>Type their username:</b> <input type = "text" name = "user_name">
         <input type = "submit" value="Invite">
      </form>

   <div>Respond to Friend Request
   
      <form action = "friendsAndInvites\acceptFriendRequest.php" method= "post">
         <b>Type their username:</b> <input type = "text" name = "user_name">
         <input type = "submit" value="Accept">
      </form>
      <form action = "friendsAndInvites\denyFriendRequest.php" method= "post">
         <b>Type their username:</b> <input type = "text" name = "user_name">
         <input type = "submit" value="Deny">
      </form>
   </div>
   <h3>Respond to Invite</h3>
      <form action = "acceptInviteFriend.php" method= "post">
         <b>Type their username:</b> <input type = "text" name = "user_name">
         <input type = "submit" value="Accept">
      </form>
      <form action = "friendsAndInvites\denyInviteFriend.php" method= "post">
         <b>Type their username:</b> <input type = "text" name = "user_name">
         <input type = "submit" value="Deny">
      </form>
         
</p>

<!-- 
   UPDATE user SET wins = wins + 1 WHERE username = 'jackie'
            -->
</body>
</html>