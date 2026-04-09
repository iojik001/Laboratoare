<?php
// ══════════════════════════════════════════════════
//  SHREK TRIVIA — Script PHP pentru AJAX
//  Returnează întrebări random în format JSON
// ══════════════════════════════════════════════════

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

$questions = [
    [
        'id' => 1,
        'question' => 'În ce an a apărut primul film Shrek?',
        'answers' => ['1999', '2001', '2003', '2005'],
        'correct' => 1,
        'fact' => 'Shrek (2001) a câștigat primul Oscar pentru cel mai bun film de animație!'
    ],
    [
        'id' => 2,
        'question' => 'Cum se numește regatul condus de Lord Farquaad?',
        'answers' => ['Far Far Away', 'Duloc', 'DreamLand', 'Camelot'],
        'correct' => 1,
        'fact' => 'Duloc era un regat obsesiv de perfect, totul trebuia să fie simetric și curat.'
    ],
    [
        'id' => 3,
        'question' => 'Ce animal este cel mai bun prieten al lui Shrek?',
        'answers' => ['Un pisoi', 'Un dragon', 'Un măgar', 'Un urs'],
        'correct' => 2,
        'fact' => 'Măgarul este dublat de Eddie Murphy în versiunea originală!'
    ],
    [
        'id' => 4,
        'question' => 'Cine este antagonistul principal din Shrek 2?',
        'answers' => ['Rumpelstiltskin', 'Prințul Fermecător', 'Zâna cea Bună', 'Lord Farquaad'],
        'correct' => 2,
        'fact' => 'Zâna cea Bună conduce o afacere cu poțiuni magice și vrea ca fiul ei să se căsătorească cu Fiona.'
    ],
    [
        'id' => 5,
        'question' => 'Cu ce se termină Shrek Forever After?',
        'answers' => [
            'Shrek dispare pentru totdeauna',
            'Shrek revine în lumea reală și trăiește fericit',
            'Fiona îl abandonează pe Shrek',
            'Rumpelstiltskin câștigă'
        ],
        'correct' => 1,
        'fact' => 'Shrek Forever After (2010) este ultimul film din seria principală.'
    ],
    [
        'id' => 6,
        'question' => 'Ce poțiune bea Shrek în Shrek 2?',
        'answers' => [
            'O poțiune de invizibilitate',
            'O poțiune care îl face mai mic',
            'O poțiune care îl transformă în om',
            'O poțiune de zbor'
        ],
        'correct' => 2,
        'fact' => 'Poțiunea îl transformă temporar într-un om frumos, dar efectul dispare la miezul nopții.'
    ],
    [
        'id' => 7,
        'question' => 'Câți tripleți au Shrek și Fiona în Shrek al Treilea?',
        'answers' => ['2', '3', '4', '5'],
        'correct' => 1,
        'fact' => 'Cei trei copii ai lui Shrek și Fiona apar pentru prima dată la sfârșitul lui Shrek al Treilea.'
    ],
    [
        'id' => 8,
        'question' => 'Cine îl dublează pe Shrek în versiunea originală?',
        'answers' => ['Jim Carrey', 'Mike Myers', 'Eddie Murphy', 'Antonio Banderas'],
        'correct' => 1,
        'fact' => 'Mike Myers a ales să folosească un accent scoțian pentru personaj!'
    ],
    [
        'id' => 9,
        'question' => 'Ce animal pazeste turnul in care era inchisa Fiona?',
        'answers' => ['Un leu', 'Un griffin', 'Un dragon', 'O hidra'],
        'correct' => 2,
        'fact' => 'Dragonul din film devine mai târziu partenerul de viață al Măgarului!'
    ],
    [
        'id' => 10,
        'question' => 'Cine este Arthur Pendragon în Shrek al Treilea?',
        'answers' => [
            'Fiul lui Shrek',
            'Vărul Fionei și moștenitorul tronului',
            'Un nou antagonist',
            'Prietenul Pisicii în Cizme'
        ],
        'correct' => 1,
        'fact' => 'Arthur (Artie) este un adolescent stângaci care nu știe că este moștenitorul tronului Far Far Away.'
    ],
    [
        'id' => 11,
        'question' => 'Ce contract semnează Shrek cu Rumpelstiltskin?',
        'answers' => [
            'Să devină rege',
            'Să primească o zi ca ogor adevărat',
            'Să capete puteri magice',
            'Să primească mlaștina înapoi'
        ],
        'correct' => 1,
        'fact' => 'Contractul îl transportă într-o lume alternativă unde Shrek nu a existat niciodată.'
    ],
    [
        'id' => 12,
        'question' => 'Studio-ul care a produs filmele Shrek este:',
        'answers' => ['Pixar', 'DreamWorks Animation', 'Warner Bros', 'Universal'],
        'correct' => 1,
        'fact' => 'DreamWorks Animation a produs Shrek ca răspuns la succesul Pixar cu Toy Story.'
    ],
];

$action = $_GET['action'] ?? 'random';

if ($action === 'random') {
    // Exclude intrebarea curenta daca e specificata
    $excludeId = isset($_GET['exclude']) ? (int)$_GET['exclude'] : -1;
    $available = array_filter($questions, fn($q) => $q['id'] !== $excludeId);

    if (empty($available)) {
        $available = $questions;
    }

    $available = array_values($available);
    $random = $available[array_rand($available)];

    // Trimite intrebarea fara raspunsul corect
    echo json_encode([
        'success'  => true,
        'id'       => $random['id'],
        'question' => $random['question'],
        'answers'  => $random['answers'],
        'total'    => count($questions)
    ]);

} elseif ($action === 'check') {
    $id     = (int)($_GET['id'] ?? 0);
    $answer = (int)($_GET['answer'] ?? -1);

    $found = null;
    foreach ($questions as $q) {
        if ($q['id'] === $id) { $found = $q; break; }
    }

    if (!$found) {
        echo json_encode(['success' => false, 'message' => 'Întrebare negăsită']);
        exit;
    }

    $isCorrect = ($answer === $found['correct']);

    echo json_encode([
        'success'        => true,
        'correct'        => $isCorrect,
        'correctIndex'   => $found['correct'],
        'correctAnswer'  => $found['answers'][$found['correct']],
        'fact'           => $found['fact']
    ]);
}
?>
