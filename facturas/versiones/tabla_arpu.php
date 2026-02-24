<h2 style="text-align: center; margin-top: 20px;">ARPU por Estado</h2>
<table style="margin: 0 auto; text-align: center; width: 50%; border-collapse: collapse; font-size: 16px; background-color: #fff; box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);">
    <tr>
        <?php foreach ($arpu as $estado => $valor): ?>
            <th style="padding: 10px; border: 1px solid #d0d7de; background-color: #e1ebf5; color: #333;">
                ARPU <?= htmlspecialchars($estado); ?>
            </th>
        <?php endforeach; ?>
    </tr>
    <tr>
        <?php foreach ($arpu as $valor): ?>
            <td style="padding: 10px; border: 1px solid #d0d7de;">
                <?= number_format($valor, 2); ?>
            </td>
        <?php endforeach; ?>
    </tr>
</table>
