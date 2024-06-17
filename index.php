<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GROUP NUMBER THREE</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function insertText(symbol) {
            const expressionInput = document.getElementById('expression');
            const startPos = expressionInput.selectionStart;
            const endPos = expressionInput.selectionEnd;
            expressionInput.value = expressionInput.value.substring(0, startPos) + symbol + expressionInput.value.substring(endPos);
            expressionInput.focus();
            expressionInput.setSelectionRange(startPos + symbol.length, startPos + symbol.length);
        }
    </script>
 
</head>
<body>
    <div class="container">
        <h1>TEST FOR VALIDATION</h1>
        <div class="button">
        <button type= "button" class= "button" onclick= "insertText('∧')">∧</button>
        <button type= "button" class= "button" onclick= "insertText('∨')">∨</button>
        <button type= "button" class= "button" onclick= "insertText('¬')">¬</button>
        <button type= "button" class= "button" onclick= "insertText('→')">→</button>
        <button type= "button" class= "button" onclick= "insertText('↔')">↔</button>
        </div>

        <p>Use the above symbol in your propositions arguments</p>
        <form method="post">
            <label for="expression">Write The Logical Expression here:</label>
            <input type="text" id="expression" name="expression" required>
            <input type="submit" value="Generate Truth Table" class="success-button"> <!-- Apply the success-button class here -->
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $expression = $_POST["expression"];
            generateTruthTable($expression);
        }

        function generateTruthTable($expression) {
            $variables = [];
            preg_match_all('/[a-zA-Z]/', $expression, $variables);
            $variables = array_unique($variables[0]);
            $numRows = pow(2, count($variables));
            $results = [];

            echo "<table border='1'>";
            echo "<tr>";
            foreach ($variables as $var) {
                echo "<th>{$var}</th>";
            }
            echo "<th>{$expression}</th>";
            echo "</tr>";

            // Generate the truth table rows in descending order
            for ($i = $numRows - 1; $i >= 0; $i--) {
                $values = [];
                foreach ($variables as $j => $var) {
                    $values[$var] = ($i >> (count($variables) - $j - 1)) & 1 ? 'T' : 'F';
                }
                echo "<tr>";
                foreach ($variables as $var) {
                    echo "<td>{$values[$var]}</td>";
                }
                $evaluated = evaluateExpression($expression, $values);
                echo "<td style='color: red;'>{$evaluated}</td>"; // Changed to red color
                echo "</tr>";
                $results[] = $evaluated;
            }
            echo "</table>";

            // Check for tautology and validity
            checkTautologyAndValidity($results);
        }

        function evaluateExpression($expression, $values) {
            foreach ($values as $var => $val) {
                $expression = str_replace($var, $val, $expression);
            }
            // Replace logical symbols with PHP logical operators
            $expression = str_replace(['T', 'F', '∧', '∨', '¬', '→', '↔'], ['true', 'false', ' && ', ' || ', ' ! ', ' => ', ' == '], $expression);
            
            // Custom replacements for implications and double implications
            $expression = preg_replace('/([a-zA-Z]+)\s*=>\s*([a-zA-Z]+)/', '(!$1 || $2)', $expression);
            $expression = preg_replace('/([a-zA-Z]+)\s*==\s*([a-zA-Z]+)/', '(($1 && $2) || (!$1 && !$2))', $expression);

            $expression = 'return ' . $expression . ';';
            $evaluated = eval($expression) ? 'T' : 'F';
            return $evaluated;
        }

        function checkTautologyAndValidity($results) {
            $isTautology = true;
            foreach ($results as $result) {
                if ($result !== 'T') {
                    $isTautology = false;
                    break;
                }
            }

            echo "<h2>Results</h2>";
            if ($isTautology) {
                echo "<p style='color: red;'>The logical expression is a tautology.</p>";
                echo "<p style='color: red;'>The argument is valid.</p>";
            } else {
                echo "<p style='color: red;'>The logical expression is not a tautology (contradiction).</p>";
                echo "<p style='color: red;'>The argument is not valid (fallacy).</p>";
            }
        }
        ?>
    </div>
</body>
</html>
