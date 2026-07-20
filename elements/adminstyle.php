<?php
$editstyleform = "
    <table>
      <tr>
        <td class=\"BoardRowB\" valign=\"top\">
          <span class=\"InputSection\">Stylesheet:</span><br/>
          ".inputTextarea("stylesheet", $stylesheet, 50, 20)."
        </td>
        <td class=\"BoardRowB\" valign=\"top\">
          <br/>
          <br/>

        </td>
      </tr>
      <tr>
        <td align=\"center\" class=\"BoardRowB\">
          ".inputSubmit("Update Stylesheet")."
        </td>
        <td>
          &nbsp;
        </td>
      </tr>
    </table>
";

?>