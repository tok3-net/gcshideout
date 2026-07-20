<?php
$edittosform = "
    <table>
      <tr>
        <td class=\"BoardRowB\" valign=\"top\">
          <span class=\"InputSection\">Terms of Service:</span><br/>
          <span class=\"InputNotes\">Required Input</span><br/>
          ".inputTextarea("termsofservice", $termsofservice, 50, 20)."
        </td>
        <td class=\"BoardRowB\" valign=\"top\">
          <br/>
          <br/>

        </td>
      </tr>
      <tr>
        <td align=\"center\" class=\"BoardRowB\">
          ".inputSubmit("Update Terms of Service")."
        </td>
        <td>
          &nbsp;
        </td>
      </tr>
    </table>
";

?>