<!DOCTYPE html>
<html lang="$ContentLocale">
<head>
    <% base_tag %>
    <title>$Title</title>
</head>
<body>
    <h1>$Title</h1>
    <%-- As docs/configuration.md "Rendering icon in template" prescribes. --%>
    <span class="page-icon">$Icon.Icon.Tag</span>
    <%-- Security renders the frontend login form through this template. --%>
    $Content
    $Form
</body>
</html>
