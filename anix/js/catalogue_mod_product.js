function updateReviews(){
	//reviews_table[i][0]: review ID
	//reviews_table[i][1]: reviewer name
	//reviews_table[i][2]: review date
	//reviews_table[i][3]: Moderated (bool 0/1)
	//reviews_table[i][4]: Review content
	str="";
	size = reviews_table.length;
	for(i=0;i<size;i++){
		str=str+"<a href='javascript:void(0);' onclick='xajax_deleteReview("+reviews_table[i][0]+")'><img src='../images/del.gif' /></a>";
		if(!reviews_table[i][3]) str+="<a href='javascript:void(0);' onclick='xajax_acceptReview("+reviews_table[i][0]+")'><img src='../images/icon_validate.jpg' /></a>";
		else str+="<img src='../images/order_blank.gif' />";
		str+="&nbsp;&nbsp;";
		str+="<a href='javascript:void(0)' onclick='javascript:showHideReview("+reviews_table[i][0]+")'>";
		str+="<b>"+reviews_table[i][1]+"</b>&nbsp;<i>"+reviews_table[i][2]+"</i>&nbsp;&nbsp;>><br />";
		str+="</a>";
		str+="<div id='review_"+reviews_table[i][0]+"' style='display:none;padding:10px 0 10 40px;'>"+reviews_table[i][4]+"</div>";
	}
	document.getElementById('reviews_list').innerHTML=str;
}

function deleteReview(id){
	size = reviews_table.length;
	for(i=0;reviews_table[i];i++){
		if(reviews_table[i][0]==id){
			reviews_table.splice(i,1);
		}
	}
	size = reviews_table.length;
	updateReviews();
}

function acceptReview(id){
	size = reviews_table.length;
	for(i=0;i<size;i++){
		if(reviews_table[i][0]==id) reviews_table[i][3]=true;
	}
	updateReviews();
}

function showHideReview($id){
	container = document.getElementById('review_'+$id);
	if(container.style.display=='none'){
		container.style.display='';
	} else {
		container.style.display='none';
	}
}