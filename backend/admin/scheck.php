<html>
   <body>
      
      <?php 
         $marks = array( 
            "english" => array (
               'lbl_hi' => 'hi',
               'lbl_hello' =>'hello' 	
            ),
            
            "franch" => array (
               'lbl_hi' => 'ih',
               'lbl_hello' =>'olleh' 	
            ),
            
            
         );
         
         /* Accessing multi-dimensional array values */
         /*echo "Marks for mohammad in physics : " ;
         echo $marks['mohammad']['physics'] . "<br />"; 
         
         echo "Marks for qadir in maths : ";
         echo $marks['qadir']['maths'] . "<br />"; 
         
         echo "Marks for zara in chemistry : " ;
         echo $marks['zara']['chemistry'] . "<br />"; */
		 foreach($marks as $key=>$value)
			{
				$value['how']='woh';
				$marks[$key]=$value;
				
				
			}
			
			foreach($marks as $key=>$value)
		 {
			 
			 foreach($value as $key=>$v)
			 {
				 
				 echo $key."&nbsp;&nbsp;&nbsp;".$v;
				 echo "<br>";
			 }
		 }
			
		 
      ?>
   
   </body>
</html>