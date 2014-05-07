#!/bin/bash

# výstupem je tabulka: průměř uživatele přes všechny hodnocení
cat $1 | awk '
BEGIN{
	user=0;
	cnt=0;
	sum=0;
}
{
	if($1!=user)
	{
		if(cnt!=0)
			printf("%f", sum/cnt );
		cnt=0;
		sum=0;
		while( user<$1 )
		{
			user++;
			printf("\n");
		}
	}
	cnt++;
	if($3+0>200.0)
		sum+=0;
	else
		sum+=$3;
}
END{
	printf( "%f\n", sum/cnt );
}
'
