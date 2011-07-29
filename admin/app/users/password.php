<form method="POST" action="<?php echo ADMIN_URL?>/users/updatepass/<?php echo $this->user->username?>">
<!-- Begin User Form -->
        <div>
     <table class='edit_win' cellpadding='6' cellspacing='0'>
       <tr>
         <td class='currentpass'><h5>Current Password</h5></td>
         <td class='edit_col firstrow'>
           <input type="password" id="curpass" name="user[curpass]" value="">
         </td>
       </tr>
       <tr>
         <td><h5>New Password</h5></td>
         <td>
           <input type="password" id="width" name="user[np1]" value="">
         </td>
       </tr>
       <tr>
         <td><h5>New Password</h5></td>
         <td>
           <input type="password" id="width" name="user[np2]" value="">
         </td>
       </table>
     </div>
	<br clear="all" />
<input value="Save Changes" type="submit" name="submit" />
</form>
