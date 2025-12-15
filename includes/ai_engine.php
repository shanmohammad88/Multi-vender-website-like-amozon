<?php
// SIMPLE AI RECOMMENDATION ENGINE

function get_recommendations($conn, $user_id) {
    $recommendations = [];

    // STRATEGY 1: PERSONALIZED (If user is logged in)
    if($user_id > 0) {
        // Find the top category this user is interested in (High Score)
        $sql = "SELECT category, SUM(interest_score) as total_score 
                FROM user_behavior 
                WHERE user_id = '$user_id' 
                GROUP BY category 
                ORDER BY total_score DESC LIMIT 1";
        
        $res = mysqli_query($conn, $sql);
        $top_interest = mysqli_fetch_assoc($res);

        if($top_interest) {
            $fav_cat = $top_interest['category'];
            
            // Recommend products from this category
            // ORDER BY RAND() gives variety, LIMIT 4 for the layout
            $rec_q = "SELECT * FROM products WHERE category = '$fav_cat' ORDER BY RAND() LIMIT 4";
            $rec_res = mysqli_query($conn, $rec_q);
            
            while($row = mysqli_fetch_assoc($rec_res)) {
                $recommendations[] = $row;
            }
        }
    }

    // STRATEGY 2: CROWD WISDOM (Fallback for Guests or Empty History)
    // If we have less than 4 items, fill the rest with "Trending" items
    if(count($recommendations) < 4) {
        // Find products with the highest TOTAL score from ALL users
        $trend_sql = "SELECT p.*, SUM(b.interest_score) as popularity 
                      FROM user_behavior b
                      JOIN products p ON b.product_id = p.id
                      GROUP BY b.product_id 
                      ORDER BY popularity DESC LIMIT 4";
        
        $trend_res = mysqli_query($conn, $trend_sql);
        while($row = mysqli_fetch_assoc($trend_res)) {
            // Avoid duplicates (Don't show the same item twice)
            $ids = array_column($recommendations, 'id');
            if(!in_array($row['id'], $ids)) {
                $recommendations[] = $row;
            }
        }
    }

    // Return the final list (limit to 4)
    return array_slice($recommendations, 0, 4);
}
?>