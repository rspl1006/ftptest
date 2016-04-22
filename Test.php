public function onlineReviewPlugin($businessid=null,$pageno = null)
    {
                      $busid = $businessid;
                  $reviews=array();
          $this->loadModel('onlineReviewPlugin');
          $this->loadModel('onlineReview');
                      $this->loadModel('Business');
          $onlineReviews = $this->onlineReviewPlugin->find('all',array('conditions'=>array('onlineReviewPlugin.business_id'=>$businessid)));

                      //now off line reviews
                      $offlineReviews = $this->Business->find('first',array('conditions'=>array('Business.id'=>$businessid)));
                      //echo "<pre>";print_r($offlineReviews['BusinessReview']);die;


          $onlinereviews = array();

          foreach ($onlineReviews as $key => $value) 
          {

          $onlinereviews[] = $this->onlineReview->find('all',array('conditions'=>array(
            'onlineReview.business_id'=>$busid,'onlineReview.social_media_id'=>$value['onlineReviewPlugin']['social_media_id']),'order'=>'onlineReview.id DESC'));  
          }  

                      //now for offline reviews


          $returnReviews = array();

          foreach ($onlinereviews as $key=>$value) {
                   foreach ($value as $val) {
                    $returnReviews[] = $val;
                   }
           }
           if($pageno ){
                    $result['page']=$pageno;
                $maxlimit=$pageno * 10;
                $minlimit = (($pageno - 1)*10);
           }else{
            $pageno = 1;		$result['page']=1;
            $maxlimit=$pageno * 10;
                $minlimit = (($pageno - 1)*10);
           }
           $result=array();
           # Format Review and return

           foreach ($returnReviews as $key => $value) 
           {
            if($key <= $maxlimit && $key >= $minlimit)
            {	
                    #print_r($value);
                 $reviews[$key]['cname']=$value['onlineReview']['CustomerFullName'];
                 $reviews[$key]['ratingdescription']=$value['onlineReview']['ratingdescription'];
                 $reviews[$key]['ratingstar']=HTTP_ROOT.'/img/'.$value['onlineReview']['ratingstar'].'stars.png';
                 $reviews[$key]['date']=$value['onlineReview']['updated'];
                 $reviews[$key]['img']=HTTP_ROOT.'/img/social-icons/'.$value['socialMedia']['mediasitename'].'.png';
            }
           else
           {
             continue;
           }
          }
                      $newKey = $key+1;

                      foreach($offlineReviews['BusinessReview'] as $reviewValue){

                              //echo "<pre>11";print_r($reviewValue);die;
                            $reviews[$newKey]['cname']=$reviewValue['firstName']." ".$reviewValue['lastName'];
                            $reviews[$newKey]['ratingdescription']=$reviewValue['ratingdescription'];
                            $reviews[$newKey]['ratingstar']=HTTP_ROOT.'/img/'.$reviewValue['ratingstar'].'stars.png';
                            $reviews[$newKey]['date']=$reviewValue['ratingdate'];
                              $newKey++;

                      }


           $this->loadModel('Business');
           $businessInfo=$this->Business->find('first',array('contain'=>false,'conditions'=>array('Business.id'=>$busid)));



           ##  $result['business']=$businessInfo['Business'];
           #####
            /*$result['business']=array(
                    'name'=>$businessInfo['Business']['businessname'],
                    'logo'=>$businessInfo['Business']['business_logo'],
                    'description'=>$businessInfo['Business']['business_description'].'Today will come safkdslkfjldskjflksjflkjdsafjldsakjf',
                                    'address'=>$businessInfo['Business']['addressline1'].''.$businessInfo['Business']['addressline2'],
            );*/


                            $result['business']=array(
                    'name'=>$businessInfo['Business']['businessname'],
                    'logo'=>$businessInfo['Business']['business_logo'],
                    'description'=>$businessInfo['Business']['business_description'],
                                    'address'=>$businessInfo['Business']['addressline1'].''.$businessInfo['Business']['addressline2'],
            );



            $result['reviews']=$reviews;
            header("Access-Control-Allow-Origin: *");
           echo json_encode($result);die;
}

Test