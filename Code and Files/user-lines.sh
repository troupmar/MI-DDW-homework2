#!/bin/bash

# výstupem je tabulka: první řádek | počet řádků
cat $1 | awk '
BEGIN{
	user=0;
	cnt=0;
	first=-1;
	line=-1;
}
{
	cnt++;
	line++;
	if($1!=user)
	{
		if(first!=-1)
			printf("%d %d", first, cnt );
		first=line;
		cnt=0;
		while( user<$1 )
		{
			user++;
			printf("\n");
		}
	}
}
END{
	cnt++;
	line++;

	if(first!=-1)
		printf("%d %d", first, cnt );
	first=line;
	cnt=0;
	printf("\n");
}
'
