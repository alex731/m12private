{literal}
<script language="JavaScript" type="text/JavaScript">
 var ptn_empty=/^\s*$/;
 var ptn_email=/^\s*\w+([\.\w\-]+)*\w@\w((\.\w)*\w+)*\.\w{2,}\s*$/;
 var ptn_phone=/^\s*(\(\d*\)\s*)?[A-Za-z0-9-]*[A-Za-z0-9]+\s*$/;
 var ptn_fax=/^\s*(\d|-)+\s*$/;
 var ptn_positiv_digit=/^\s*(\d+)\s*$/;
 var ptn_domain_name=/^(\w)[-\w]+\w\.(\w+)$/;

 function check_empty(field, name)
 {
    if (!field.value || ptn_empty.test(field.value))
    {
        error = "\"The field '\"+name+\"' is empty. Fill it, please.\"";
        alert(error); field.focus(); field.select();
        return false;
    }
    return true;
 }

 function check(field, pattern, text_msg)
 {
	if (field.value && pattern.test(field.value) == false)
    {
        alert(text_msg);
		field.select();
		field.focus(); 
        return false;
    }
    return true;
 }
</script>
{/literal}