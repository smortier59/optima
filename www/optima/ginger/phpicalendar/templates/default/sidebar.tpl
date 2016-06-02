<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="center" width="112" class="sideback"><font class="G10BOLD">{SIDEBAR_DATE}</font></td>
	</tr>
	<tr>
		<td colspan="3" bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<b>{L_LEGEND}:</b><br />
				{LEGEND}
			</div>
		</td>
	</tr>
</table>
<form method="get" action="week.php">
	<input type="hidden" name="cpath" value="{CPATH}"/>
	<input type="hidden" name="getdate" value="{GETDATE}"/>
    <table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
        <tr>
            <td>
                {LISTING_USER}
            </td>
        </tr>
        <tr>
        	<td>
            	<input type="submit" value="Go"/>
            </td>
        </tr>
    </table>
</form>
