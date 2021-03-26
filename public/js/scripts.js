
function convertTime(diff){
    var convtime;
    var convtime2;
    var convtime3;

    if(diff<60)
    {
        convtime="just now";
    }
    else
    {
        if(Math.round(diff / 60)>0);
        {
            convtime2=diff /60;
            if(convtime2>1)
            {
                convtime3=" minutes";
            }
            else
            {
                convtime3=" minute";
            }
            convtime=Math.round(convtime2)+convtime3 +" ago";
        }
        if(Math.round(diff / 3600) >0)
        {
            convtime2=diff /  3600;
            convtime2=Math.round(convtime2);
            if(convtime2>1)
            {
                convtime3=" hours";
            }
            else
            {
                convtime3=" hour";
            }
            convtime=Math.round(convtime2)+convtime3+" ago";
        }
        if(Math.round(diff / 84600)>0)
        {
            convtime2=diff / 84600;
            convtime2=Math.round(convtime2);
            if(convtime2>1)
            {
                convtime3=" days";
            }
            else
            {
                convtime3=" day";
            }
            convtime=Math.round(convtime2)+convtime3+" ago";
        }

        if(Math.round(diff / 604800)>0)
        {
            convtime2=diff / 604800;
            convtime2=Math.round(convtime2);
            if(convtime2>1)
            {
                convtime3=" weeks";
            }
            else
            {
                convtime3=" week";
            }
            convtime=Math.round(convtime2)+convtime3+" ago";
        }

        if(Math.round(diff / 2.628e+6)>0)
        {
            convtime2=diff / 2.628e+6;
            convtime2=Math.round(convtime2);
            if(convtime2>1)
            {
                convtime3=" months";
            }
            else
            {
                convtime3=" month";
            }
            convtime=Math.round(convtime2)+convtime3+" ago";
        }

        if(Math.round(diff / 3.154e+7)>0)
        {
            convtime2=diff / 3.154e+7;
            convtime2=Math.round(convtime2);
            if(convtime2>1)
            {
                convtime3=" years";
            }
            else
            {
                convtime3=" year";
            }
            convtime=Math.round(convtime2)+convtime3+" ago";
        }
    }
    var convertedtime=convtime;
    return convertedtime;
}