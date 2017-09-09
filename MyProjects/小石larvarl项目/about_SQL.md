#此文件用来记录一些学习sql过程中学到的值得注意的地方，注：并不讲述基础语句知识，只讲述部分效率问题

1.关于索引--不同版本的数据库版本对索引等的支持可能不同。
	主键和唯一键都属于索引，而且某些情况下的使用效率高于普通索引。

	查询语句执行过程中，不论有多少个索引，单词查询中只能使用一个索引，因此合适的联合索引是十分有必要的。

	a.简单说明索引会增加空间开销以及更新或插入表的开销，但是提高查询效率，索引字段出现在where或orderby字段中时会提高效率。

	b.联合索引，联合索引的使用比较复杂，简单说明即：联合索引按照顺序使用的时候，无论使用几个，都是会用到索引的，反之则不会。
		举例：若字段a,b,c有联合索引，那么同时使用到a, a|b, a|b|c这三种情况会使用到索引，b, c, b|c都不会使用到索引
		简单的比较说明:(以下例子建立在a,b,c三个字段联合索引的情况下)可以简单理解为大部分情况下出现在where和orderby子句中并且索引字段是用的是'等于'类型的比较判断，才会使用到索引的。
				优: select * from test where a=10 and b>50
				差: select * from test where b>50

				优: select * from test order by a
				差: select * from test order by b
				差: select * from test order by c

				优: select * from test where a=10 order by a
				优: select * from test where a=10 order by b
				差: select * from test where a=10 order by c

				优: select * from test where a>10 order by a 		#这一句虽然a>10已经是一个全表扫描了，但是orderby a是可以用到索引的
				差: select * from test where a>10 order by b  		#这一句因为a>10已经是一个全表扫描了，因此orderby b时用不到索引效率低
				差: select * from test where a>10 order by c

				优: select * from test where a=10 and b=10 order by a
				优: select * from test where a=10 and b=10 order by b
				优: select * from test where a=10 and b=10 order by c

				优: select * from test where a=10 and b=10 order by a
				优: select * from test where a=10 and b>10 order by b
				差: select * from test where a=10 and b>10 order by c

	c.关于索引是否会被有效使用，常用到的可能用错的项用------标明
		1.对查询进行优化，应尽量避免全表扫描，首先应考虑在 where 及 order by 涉及的列上建立索引。

		-------2.应尽量避免在 where 子句中对字段进行 null 值判断，否则将导致引擎放弃使用索引而进行全表扫描，如：
			select id from t where num is null
			可以在num上设置默认值0，确保表中num列没有null值，然后这样查询：
			select id from t where num=0

		-------3.应尽量避免在 where 子句中使用!=或<>操作符，否则将引擎放弃使用索引而进行全表扫描。

		-------4.应尽量避免在 where 子句中使用 or 来连接条件，否则将导致引擎放弃使用索引而进行全表扫描，如：
			select id from t where num=10 or num=20
			可以这样查询：
			select id from t where num=10
			union all
			select id from t where num=20 

		5.in 和 not in 也要慎用，否则会导致全表扫描，如：
			select id from t where num in(1,2,3)
			对于连续的数值，能用 between 就不要用 in 了：
			select id from t where num between 1 and 3

		6.下面的查询也将导致全表扫描：
			select id from t where name like '%abc%'
			若要提高效率，可以考虑全文检索。 

		-------7.如果在 where 子句中使用参数，也会导致全表扫描。因为SQL只有在运行时才会解析局部变量，但优化程序不能将访问计划的选择推迟到运行时；它必须在编译时进行选择。然而，如果在编译时建立访问计划，变量的值还是未知的，因而无法作为索引选择的输入项。如下面语句将进行全表扫描：
			select id from t where num=@num
			可以改为强制查询使用索引：
			select id from t with(index(索引名)) where num=@num

		-------8.应尽量避免在 where 子句中对字段进行表达式操作，这将导致引擎放弃使用索引而进行全表扫描。如：
			select id from t where num/2=100
			应改为对常量进行操作:
			select id from t where num=100*2	

		-------9.应尽量避免在where子句中对字段进行函数操作，这将导致引擎放弃使用索引而进行全表扫描。如：
			select id from t where substring(name,1,3)='abc'--name以abc开头的id
			select id from t where datediff(day,createdate,'2005-11-30')=0--‘2005-11-30’生成的id
			应改为:
			select id from t where name like 'abc%'
			select id from t where createdate>='2005-11-30' and createdate<'2005-12-1'

		-------10.不要在 where 子句中的“=”左边进行函数、算术运算或其他表达式运算，否则系统将可能无法正确使用索引。

		-------11.在使用索引字段作为条件时，如果该索引是复合索引，那么必须使用到该索引中的前几个字段作为条件时才能保证系统使用该索引，否则该索引将不会被使用，并	且应尽可能的让字段顺序与索引顺序相一致，这一点在联合索引里有详细解释。

		12.不要写一些没有意义的查询，如需要生成一个空表结构：
		select col1,col2 into #t from t where 1=0
		这类代码不会返回任何结果集，但是会消耗系统资源的，应改成这样：
		create table #t(...)

		-------13.很多时候用 exists 代替 in 是一个好的选择：
			select num from a where num in(select num from b)
			用下面的语句替换：
			select num from a where exists(select 1 from b where num=a.num)
		-------14.并不是所有索引对查询都有效，SQL是根据表中数据来进行查询优化的，当索引列有大量数据重复时，SQL查询可能不会去利用索引，如一表中有字段sex，male、female几乎各一半，那么即使在sex上建了索引也对查询效率起不了作用，这一点要求我们建立索引的时候要考虑这个字段是否值得创建索引。

		15.索引并不是越多越好，索引固然可以提高相应的 select 的效率，但同时也降低了 insert 及 update 的效率，因为 insert 或 update 时有可能会重建索引，所以怎样建索引需要慎重考虑，视具体情况而定。一个表的索引数最好不要超过6个，若太多则应考虑一些不常使用到的列上建的索引是否有必要。 

		16.应尽可能的避免更新 clustered 索引数据列，因为 clustered 索引数据列的顺序就是表记录的物理存储顺序，一旦该列值改变将导致整个表记录的顺序的调整，会耗费相当大的资源。若应用系统需要频繁更新 clustered 索引数据列，那么需要考虑是否应将该索引建为 clustered 索引。 

		-------17.尽量使用数字型字段，若只含数值信息的字段尽量不要设计为字符型，这会降低查询和连接的性能，并会增加存储开销。这是因为引擎在处理查询和连接时会逐个比较字符串中每一个字符，而对于数字型而言只需要比较一次就够了。

		-------18.尽可能的使用 varchar/nvarchar 代替 char/nchar ，因为首先变长字段存储空间小，可以节省存储空间，其次对于查询来说，在一个相对较小的字段内搜索效率显然要高些。

		-------19.任何地方都不要使用
			select * from t 
			用具体的字段列表代替“*”，不要返回用不到的任何字段，只返回需要用到的值，只返回需要用到的值，只返回需要用到的值！

		20.尽量使用表变量来代替临时表。如果表变量包含大量数据，请注意索引非常有限（只有主键索引）。

		-------21.避免频繁创建和删除临时表，以减少系统表资源的消耗。

		22.临时表并不是不可使用，适当地使用它们可以使某些例程更有效，例如，当需要重复引用大型表或常用表中的某个数据集时。但是，对于一次性事件，最好使用导出表。

		23.在新建临时表时，如果一次性插入数据量很大，那么可以使用 select into 代替 create table，避免造成大量 log ，以提高速度；如果数据量不大，为了缓和系统表的资源，应先create table，然后insert。

		24.如果使用到了临时表，在存储过程的最后务必将所有的临时表显式删除，先 truncate table ，然后 drop table ，这样可以避免系统表的较长时间锁定。

		25.尽量避免使用游标，因为游标的效率较差，如果游标操作的数据超过1万行，那么就应该考虑改写。

		26.使用基于游标的方法或临时表方法之前，应先寻找基于集的解决方案来解决问题，基于集的方法通常更有效。

		27.与临时表一样，游标并不是不可使用。对小型数据集使用 FAST_FORWARD 游标通常要优于其他逐行处理方法，尤其是在必须引用几个表才能获得所需的数据时。在结果集中包括“合计”的例程通常要比使用游标执行的速度快。如果开发时间允许，基于游标的方法和基于集的方法都可以尝试一下，看哪一种方法的效果更好。

		28.在所有的存储过程和触发器的开始处设置 SET NOCOUNT ON ，在结束时设置 SET NOCOUNT OFF 。无需在执行存储过程和触发器的每个语句后向客户端发送 DONE_IN_PROC 消息。

		29.尽量避免大事务操作，提高系统并发能力。

		-------30.尽量避免向客户端返回大数据量，若数据量过大，应该考虑相应需求是否合理，能做好统计就在数据库中做好统计。

		-------31:查询语句使用like关键字进行查询，如果匹配的第一个字符为”%“时，索引不会被使用
		     select * from student where num like '%4';          //索引不会被使用
		     select * from student where num like '4%';         //索引会被使用

	d. 	使用聚集索引
		聚集索引确定表中数据的物理顺序。聚集索引类似于电话簿。由于聚集索引规定数据在表中的物理存储顺序，因此一个表只能包含一个聚集索引。但该索引可以包含多个列（组合索引），就像电话簿按姓氏和名字进行组织一样。
		聚集索引对于那些经常要搜索范围值的列特别有效。使用聚集索引找到包含第一个值的行后，便可以确保包含后续索引值的行在物理相邻。避免每次查询该列时都进行排序，从而节省成本。
		注意事项
		定义聚集索引键时使用的列越少越好。 
		• 包含大量非重复值的列。
		• 使用下列运算符返回一个范围值的查询：BETWEEN、>、>=、< 和 <=。
		• 被连续访问的列。
		• 返回大型结果集的查询。
		• 经常被使用联接或 GROUP BY 子句的查询访问的列；一般来说，这些是外键列。对 ORDER BY 或 GROUP BY 子句中指定的列进行索引，可以使 SQL Server 不必对数据进行排序，因为这些行已经排序。这样可以提高查询性能。
		• OLTP 类型的应用程序，这些程序要求进行非常快速的单行查找（一般通过主键）。应在主键上创建聚集索引。 
		聚集索引不适用于： 
		• 频繁更改的列 。这将导致整行移动（因为 SQL Server 必须按物理顺序保留行中的数据值）。这一点要特别注意，因为在大数据量事务处理系统中数据是易失的。
		• 宽键 。来自聚集索引的键值由所有非聚集索引作为查找键使用，因此存储在每个非聚集索引的叶条目内。

		CREATE [ UNIQUE ] CLUSTERED INDEX index_name
		    ON { table | view } ( column [ ASC | DESC ] [ ,...n ] )


	e.	使用非聚集索引
		非聚集索引与课本中的目录类似。数据存储在一个地方，索引存储在另一个地方，索引带有指针指向数据的存储位置。索引中的项目按索引键值的顺序存储，而表中的信息按另一种顺序存储（这可以由聚集索引规定）。如果在表中未创建聚集索引，则无法保证这些行具有任何特定的顺序。
		多个非聚集索引
		有些书籍包含多个索引。例如，一本介绍园艺的书可能会包含一个植物通俗名称索引，和一个植物学名索引，因为这是读者查找信息的两种最常用的方法。对于非聚集索引也是如此。可以为在表中查找数据时常用的每个列创建一个非聚集索引。
		注意事项
		在创建非聚集索引之前，应先了解您的数据是如何被访问的。可考虑将非聚集索引用于： 
		• 包含大量非重复值的列，如姓氏和名字的组合（如果聚集索引用于其它列）。如果只有很少的非重复值，如只有 1 和 0，则大多数查询将不使用索引，因为此时表扫描通常更有效。
		• 不返回大型结果集的查询。
		• 返回精确匹配的查询的搜索条件（WHERE 子句）中经常使用的列。
		• 经常需要联接和分组的决策支持系统应用程序。应在联接和分组操作中使用的列上创建多个非聚集索引，在任何外键列上创建一个聚集索引。
		• 在特定的查询中覆盖一个表中的所有列。这将完全消除对表或聚集索引的访问。 

		CREATE [ UNIQUE ]  NONCLUSTERED  INDEX index_name
		    ON { table | view } ( column [ ASC | DESC ] [ ,...n ] ) 

	f. 	INCLUDE 子句规定可以往索引键码中追加另外的列。对于某些使用只访问索引的查询，这些追加的列将有利于提高性能，但要求表中每行都是唯一的。该选项可以实现下述功能：

			● 使更多的查询省去存取数据页的需要。
			● 消除冗余的索引。
			● 维护索引的惟一性。

		这里提高效率是因为，使用索引查询时，会查询索引页，如果查询的字段并非索引，那么就会根据索引在数据页里搜索需要的字段，会降低效率。
		因此如果把数据捆绑起来都放在索引页中，那么查询索引页就可以得到所有需要的信息，因此就不需要再去数据页里查询。

		等于新建立了一个比较小的表。 

	g. 	双向索引

		一个使用CREATE INDEX语句中的ALLOW REVERSE SCANS参数创建的单索引可以向左或者向右扫描。也就是说，这些索引支持按照在反方向创建和扫描索引时所定义的方向索引。这个SQL语句如下。

		CREATE INDEX iname ON tname(cname DESC) ALLOW REVERSE SCANS 
		在这种情况下，基于给定列(cname)中的递减值(DESC)形成索引(iname)。尽管列上的索引定义用来按照递减次序扫描，通过允许反向扫描，可以按照降序(反向)扫描。实际上没有使用这两个方向上的索引，创建和考虑存取模式时由优化器控制这些索引的使用。


2.MYSQL执行状态解释
	a.MYSQL执行过程中会有各种的状态，每个状态做特定的事物并且每个状态消耗的时间可能给我们一些优化的参考。

	b.相关命令说明：show processlist   显示当前的进程，一般会包括以下几个列
		 Id | User | Host | db | Command | Time| State | Info
	  说明各列的含义和用途：
		id列:一个标识，你要kill 一个语句的时候很有用。
		user列: 显示当前用户，如果不是root，这个命令就只显示你权限范围内的sql语句。
		host列:显示这个语句是从哪个ip 的哪个端口上发出的。可用来追踪出问题语句的用户。
		db列:显示这个进程目前连接的是哪个数据库。
		command列:显示当前连接的执行的命令，一般就是休眠（sleep），查询（query），连接（connect）。
		time列:这个状态持续的时间，单位是秒，语句执行过慢可以在这里直观的看到。
		state列:显示使用当前连接的sql语句的状态，很重要的列，请注意，state只显示语句当前执行到的阶段，一个sql语句，以查询为例，可能需要经过copying to tmp table，Sorting result，Sending data等状态才可以完成。
		info列:显示这个sql语句，因为长度有限，所以长的sql语句就显示不全，通常用来判断是哪个语句。

	c.具体状态解释:
		Checking table------------正在检查数据表（这是自动的）
		Closing tables------------正在将表中修改的数据刷新到磁盘中，同时正在关闭已经用完的表。这是一个很快的操作，如果不是这样的话，就应该确认磁盘空间是否已经满了或者磁盘是否正处于重负中。
		Connect Out----------复制从服务器正在连接主服务器。
		Copying to tmp table on disk-----------由于临时结果集大于tmp_table_size，正在将临时表从内存存储转为磁盘存储以此节省内存，经验看来如果执行这一过程，那么语句执行时间将会很久。
		Creating tmp table-----------正在创建临时表以存放部分查询结果。
		deleting from main table------------服务器正在执行多表删除中的第一部分，刚删除第一个表。
		deleting from reference tables------------服务器正在执行多表删除中的第二部分，正在删除其他表的记录。
		Flushing tables------------正在执行 FLUSH TABLES，等待其他线程关闭数据表。
		Killed-----------发送了一个kill请求给某线程，那么这个线程将会检查kill标志位，同时会放弃下一个kill请求。MySQL会在每次的主循环中检查kill标志位， 不过有些情况下该线程可能会过一小段才能死掉。如果该线程程被其他线程锁住了，那么kill请求会在锁释放时马上生效。
		Locked---------------被其他查询锁住了。
		Sending data--------------正在处理 SELECT 查询的记录，同时正在把结果发送给客户端。
		Sorting for group-------------正在为 GROUP BY 做排序。
		Sorting for order-------------正在为 ORDER BY 做排序。
		Opening tables--------------这个过程应该会很快，除非受到其他因素的干扰。例如，在执 ALTER TABLE 或 LOCK TABLE 语句行完以前，数据表无法被其他线程打开。 正尝试打开一个表。
		Removing duplicates--------------正在执行一个 SELECT DISTINCT 方式的查询，但是MySQL无法在前一个阶段优化掉那些重复的记录。因此，MySQL需要再次去掉重复的记录，然后再把结果发送给客户端。
		Reopen table--------------获得了对一个表的锁，但是必须在表结构修改之后才能获得这个锁。已经释放锁，关闭数据表，正尝试重新打开数据表。
		Repair by sorting-------------修复指令正在排序以创建索引。
		Repair with keycache-------------修复指令正在利用索引缓存一个一个地创建新索引。它会比 Repair by sorting 慢些。
		Searching rows for update-------------正在讲符合条件的记录找出来以备更新。它必须在 UPDATE 要修改相关的记录之前就完成了。
		Sleeping-----------正在等待客户端发送新请求.
		System lock------------正在等待取得一个外部的系统锁。如果当前没有运行多个 mysqld 服务器同时请求同一个表，那么可以通过增加 --skip-external-locking参数来禁止外部系统锁。
		Upgrading lock-------------INSERT DELAYED 正在尝试取得一个锁表以插入新记录。
		Updating--------------正在搜索匹配的记录，并且修改它们。
		User Lock-------------正在等待 GET_LOCK()。
		Waiting for tables------------该 线程得到通知，数据表结构已经被修改了，需要重新打开数据表以取得新的结构。然后，为了能的重新打开数据表，必须等到所有其他线程关闭这个表。以下几种情 况下会产生这个通知：FLUSH TABLES tbl_name, ALTER TABLE, RENAME TABLE, REPAIR TABLE, ANALYZE TABLE, 或 OPTIMIZE TABLE。
		waiting for handler insert------------INSERT DELAYED 已经处理完了所有待处理的插入操作，正在等待新的请求。

	d.可能出现的耗时长的状态以及可能的解决方法。
		Copying to tmp table on disk-------这一状态耗时过长是因为临时结果（join等操作生成表）数据量比较大，超过了mysql设置中的可接受的临时表大小，因此需要把结果先存进服务器的硬盘中。
							这里有两个参数会影响可接受的临时表大小，分别是tmp_table_size和max_heap_table_size，真正的参考值是这两个变量中的较小值，请注意这个参考值是mysql分配给每个进程的内存值，因此调整的时候需要根据服务器的内存大小以及服务器上执行的进行数量进行权衡考虑。相比于修改数据库服务器的参数，其实语句也需要精简，不要做过多的无用的表连接，可以试着把复杂的统计等操作分开成几次执行，因为发生copying to tmp table和不发生copying to tmp talbe，效率会差非常多，拆成几次但是不发生这一状态，也许也能提高语句执行效率。

		Sending data--------这一状态耗时过长可能是因为查询字段的数据量过大，在一些使用索引的查询结束后，最原始的数据中可能只有索引的字段以及主键等信息，并不完全包括了我们需要的所有字段，因此可能会到数据库中重新查找所需要且还没有拿到的字段，最后还要把这些字段返回给查询的用户。如果中间有某些字段有非常大的数据量，比如出现类似于varchar(1000)等的值，就会大大减缓语句的执行速度。
		解决方案有--------首先，尽量只取必须的字段，不要使用*这样的查询。
						  其次，先简单介绍一下mysql innodb的存储以及溢出方式，在mysql innodb存储引擎表收到页块大小，数据以B+树的方式组织数据，导致单行数据不能超过8k，从而影响了表中大字段数据类型varchar，text，blob个数限制，在16k页块大小下，最好不要超过10个，在表设计中需要注意这个限制。当单行数据量大于8K时，会发生行溢出，将会保留每个字段的前768字节（对于utf-8编码格式，每个字符占3个字节，那么其实768个字节就是习惯上的255个字符，所以声明varchar()的时候经常看到varchar(255)这种长度），如果只保留每个字段的前768字节的情况下依然大于8K，就会发生报错。
						  因此，当声明了某个很长的varchar()或者text类型的时候，如果发生了行溢出，那么如果还需要这个发生了溢出的字段，就需要到溢出页(溢出页并不会保存在缓存中)去读取数据，而到溢出页去读取数据，是随机读取，而不是在当前页的顺序读取，也会很大程序上影响效率。因此建议可以根据自己使用的数据库引擎，设置很大的值的时候分开存储成多个字段，并且设置成小于发生溢出时的页面保留值以下，举例说明，mysql innodb下，存储utf-8编码格式的数据时，建议设置长度为varchar(255)类似的长度，如果都设置为这种类似的长度，发生行溢出的时候会发现各字段的保留值依然保留了所有的值，会直接报错而发现问题。
						  综上，Sending data过程过长解决方法就是尽量只取需要的字段，并且精简信息，不要保留过长的文本信息等。

3.关于explain的使用
	a.关于用法
		-explain talbe_name 可以展示一个表的所有字段信息
		-explain (sql 语句)	可以展示一个sql语句各个字句执行时用到的信息
	b.详细解释第二种用法，即解释字句
		直接在SQL语句前面加explain
		结果中包含几个字段以供参考
			1. id
				SELECT识别符。这是SELECT查询序列号。对效率查看并没有太多有用的信息。

			2. select_type
				select类型，它有以下几种值
					2.1 simple 它表示简单的select,没有union和子查询
					2.2 primary 最外面的select,在有子查询的语句中，最外面的select查询就是primary,上图中就是这样
					2.3 union union语句的第二个或者说是后面那一个.现执行一条语句，explain 
					select * from uchome_space limit 10 union select * from uchome_space limit 10,10
					第二条语句使用了union
					2.4 dependent union    UNION中的第二个或后面的SELECT语句，取决于外面的查询
					2.5 union result        UNION的结果
					还有几个参数，不重要

			3. table
				输出的行所用的表，这个参数显而易见，容易理解

			4. type
				连接类型。有多个参数，这是最重要的字段
					4.1 system
					表仅有一行，这是const类型的特列，平时不会出现，这个也可以忽略不计

					4.2 const
					表最多有一个匹配行，const用于比较primary key 或者unique索引。因为只匹配一行数据，所以很快
					记住一定是用到primary key 或者unique，并且只检索出一条数据的 情况下才会是const,看下面这条语句
						explain SELECT * FROM `asj_admin_log` limit 1	虽然只搜索一条数据,但是因为没有用到指定的索引,所以不会使用const.
					继续看下面这个
						explain SELECT * FROM `asj_admin_log` where log_id = 111	log_id是主键，所以使用了const。所以说可以理解为const是最优化的

					4.3 eq_ref
					对于eq_ref的解释，mysql手册是这样说的:
					"对于每个来自于前面的表的行组合，从该表中读取一行。这可能是最好的联接类型，除了const类型。它用在一个索引的所有部分被联接使用并且索引是UNIQUE或PRIMARY KEY"
					eq_ref可以用于使用=比较带索引的列。看下面的语句
					explain select * from uchome_spacefield,uchome_space where uchome_spacefield.uid = uchome_space.uid
					mysql会使用eq_ref联接来处理uchome_space表。

					sql语句如果变成
						explain select * from uchome_space,uchome_spacefield where uchome_space.uid = uchome_spacefield.uid
						结果还是一样，需要说明的是uid在这两个表中都是primary

					4.4 ref 对于每个来自于前面的表的行组合，所有有匹配索引值的行将从这张表中读取。如果联接只使用键的最左边的前缀，或如果键不是UNIQUE或PRIMARY KEY（换句话说，如果联接不能基于关键字选择单个行的话），则使用ref。如果使用的键仅仅匹配少量行，该联接类型是不错的。

					4.5 ref_or_null 该联接类型如同ref，但是添加了MySQL可以专门搜索包含NULL值的行。在解决子查询中经常使用该联接类型的优化。


					上面这五种情况都是很理想的索引使用情况

					4.6 index_merge 
						该联接类型表示使用了索引合并优化方法。在这种情况下，key列包含了使用的索引的清单，key_len包含了使用的索引的最长的关键元素。

					4.7 unique_subquery 
						子查询使用了unique或者primary key

					4.8 index_subquery
						子查询使用了普通索引

					4.9 range 给定范围内的检索，使用一个索引来检查行。看下面两条语句

						explain select * from uchome_space where uid in (1,2)

						explain select * from uchome_space where groupid in (1,2)

						uid有索引，groupid没有索引，结果是第一条语句的联接类型是range,第二个是ALL.以为是一定范围所以说像 between也可以这种联接,很明显

						explain select * from uchome_space where friendnum = 17

						这样的语句是不会使用range的，它会使用更好的联接类型就是上面介绍的ref（因为只有一个值）

					4.10 index	该联接类型与ALL相同，除了只有索引树被扫描。这通常比ALL快，因为索引文件通常比数据文件小。（也就是说虽然all和Index都是读全表，但index是从索引中读取的，而all是从硬盘中读的），但是索引结束后如果选取的字段并不是索引字段，还需要到数据表中根据主键去读取信息，属于sending data的状态
					当查询只使用作为单索引一部分的列时，MySQL可以使用该联接类型。

					4.11  ALL  对于每个来自于先前的表的行组合，进行完整的表扫描。如果表是第一个没标记const的表，这通常不好，并且通常在它情况下很差。通常可以增加更多的索引而不要使用ALL，使得行能基于前面的表中的常数值或列值被检索出。
			
			5. possible_keys 
				possible_keys字段是指 mysql在搜索表记录时可能使用哪个索引。注意，这个字段完全独立于explain 显示的表顺序。这就意味着 possible_keys里面所包含的索引可能在实际的使用中没用到。如果这个字段的值是null，就表示没有索引被用到。这种情况下，就可以检查 where子句中哪些字段那些字段适合增加索引以提高查询的性能。就这样，创建一下索引，然后再用explain 检查一下。详细的查看章节"14.2.2 alter tablesyntax"。想看表都有什么索引，可以通过 show index from tbl_name来看。

			6. keys 
				key字段显示了mysql实际上要用的索引。当没有任何索引被用到的时候，这个字段的值就是null。想要让mysql强行使用或者忽略在 possible_keys字段中的索引列表，可以在查询语句中使用关键字force index, use index,或 ignore index。如果是 myisam 和 bdb 类型表，可以使用 analyzetable 来帮助分析使用使用哪个索引更好。如果是 myisam类型表，运行命令 myisamchk --analyze也是一样的效果。

			7. key_len 
				key_len 字段显示了mysql使用索引的长度。当 key 字段的值为 null时，索引的长度就是 null。注意，key_len的值可以告诉你在联合索引中mysql会真正使用了哪些索引。

			8. ref   ref列显示使用哪个列或常数与key一起从表中选择行。

			9. rows 显示MYSQL执行查询的行数，简单且重要，数值越大越不好，说明没有用好索引

			10 Extra  该列包含MySQL解决查询的详细信息。

				10.1 Distinct     MySQL发现第1个匹配行后，停止为当前的行组合搜索更多的行。

				10.2 Not exists  
					mysql在查询时做一个 left join优化时，当它在当前表中找到了和前一条记录符合 left join条件后，就不再搜索更多的记录了。下面是一个这种类型的查询例子：

					select * from t1 left join t2 on t1.id=t2.id where t2.id isnull;

				10.3 range checked for each record
					mysql没找到合适的可用的索引。取代的办法是，对于前一个表的每一个行连接，它会做一个检验以决定该使用哪个索引（如果有的话），并且使用这个索引来从表里取得记录。这个过程不会很快，但总比没有任何索引时做表连接来得快。

				10.4 using filesort    
					mysql需要额外的做一遍从而以排好的顺序取得记录。排序程序根据连接的类型遍历所有的记录，并且将所有符合 where条件的记录的要排序的键和指向记录的指针存储起来。这些键已经排完序了，对应的记录也会按照排好的顺序取出来。

				10.5 using index 只使用索引树中的信息而不需要进一步搜索读取实际的行来检索表中的信息。这个比较容易理解，就是说明是否使用了索引并且所选取的字段是否已经是索引中。因为如果选取的字段不在索引中，就需要去实际的表中用主键去查询需要的字段。
				（以下语句的测试可能与数据量以及数据库版本等有关）

				explain select uid from ucspace_uchome where uid = 1		的extra为using index（uid建有索引）

				explain select * from ucspace_uchome where uid = 1		的extra为using where（uid建有索引，但还需要其他字段的信息）

				explain select count(*) from uchome_space where groupid=1 		的extra为using where(groupid未建立索引)

				10.6 using temporary

					为了解决查询，MySQL需要创建一个临时表来容纳结果。典型情况如查询包含可以按不同情况列出列的GROUP BY和ORDER BY子句时。

					出现using temporary就说明语句需要优化了，举个例子来说

					EXPLAIN SELECT ads.id FROM ads, city WHERE   city.city_id = 8005   AND ads.status = 'online'   AND city.ads_id=ads.id ORDER BY ads.id desc

					id  select_type  table   type    possible_keys   key      key_len  ref                     rows  filtered  Extra                          
					------  -----------  ------  ------  --------------  -------  -------  --------------------  ------  --------  -------------------------------
					     1  SIMPLE       city    ref     ads_id,city_id  city_id  4        const                   2838    100.00  Using temporary; Using filesort
					     1  SIMPLE       ads     eq_ref  PRIMARY         PRIMARY  4        city.ads_id       1    100.00  Using where    


					这条语句会使用using temporary,而下面这条语句则不会


					EXPLAIN SELECT ads.id FROM ads, city WHERE   city.city_id = 8005   AND ads.status = 'online'   AND city.ads_id=ads.id ORDER BY city.ads_id desc

					id  select_type  table   type    possible_keys   key      key_len  ref                     rows  filtered  Extra                      
					------  -----------  ------  ------  --------------  -------  -------  --------------------  ------  --------  ---------------------------
					     1  SIMPLE       city    ref     ads_id,city_id  city_id  4        const                   2838    100.00  Using where; Using filesort
					     1  SIMPLE       ads    eq_ref  PRIMARY         PRIMARY  4        city.ads_id       1    100.00  Using where    


					这是因为MySQL表关联的算法是 Nest Loop Join，是通过驱动表的结果集作为循环基础数据，然后一条一条地通过该结果集中的数据作为过滤条件到下一个表中查询数据，然后合并结果。EXPLAIN 结果中，第一行出现的表就是驱动表（Important!）以上两个查询语句，驱动表都是 city，如上面的执行计划所示！

					对驱动表可以直接排序，对非驱动表（的字段排序）需要对循环查询的合并结果（临时表）进行排序（Important!）
					因此，order by ads.id desc 时，就要先 using temporary 了！

					驱动表的定义
						wwh999 在 2006年总结说，当进行多表连接查询时， [驱动表] 的定义为：
						1）指定了联接条件时，满足查询条件的记录行数少的表为[驱动表]；
						2）未指定联接条件时，行数少的表为[驱动表]（Important!）。
						永远用小结果集驱动大结果集

				10.7 using where
					WHERE子句用于限制哪一个行匹配下一个表或发送到客户。除非你专门从表中索取或检查所有行，如果Extra值不为Using where并且表联接类型为ALL或index，查询可能会有一些错误。（这个说明不是很理解，因为很多很多语句都会有where条件，而type为all或index只能说明检索的数据多，并不能说明错误，useing where不是很重要，但是很常见）
					如果想要使查询尽可能快，应找出Using filesort 和Using temporary的Extra值。

				10.8 Using sort_union(...), Using union(...),Using intersect(...)
					这些函数说明如何为index_merge联接类型合并索引扫描

				10.9 Using index for group-by
					类似于访问表的Using index方式，Using index for group-by表示MySQL发现了一个索引，可以用来查询GROUP BY或DISTINCT查询的所有列，而不要额外搜索硬盘访问实际的表。并且，按最有效的方式使用索引，以便对于每个组，只读取少量索引条目。

4.杂项记录
	a.	union和union all的区别
		Union因为要进行重复值扫描，所以效率低。如果合并没有刻意要删除重复行，那么就使用Union All

		两个要联合的SQL语句 字段个数必须一样，而且字段类型要“相容”（一致）；

		如果我们需要将两个select语句的结果作为一个整体显示出来，我们就需要用到union或者union all关键字。union(或称为联合)的作用是将多个结果合并在一起显示出来。 

		union和union all的区别是

		-----------------union会自动压缩多个结果集合中的重复结果，而union all则将所有的结果全部显示出来，不管是不是重复------------------- 

		Union：对两个结果集进行并集操作，不包括重复行，同时进行默认规则的排序； 

		Union All：对两个结果集进行并集操作，包括重复行，不进行排序； 

		Intersect：对两个结果集进行交集操作，不包括重复行，同时进行默认规则的排序； 

		Minus：对两个结果集进行差操作，不包括重复行，同时进行默认规则的排序。 

		可以在最后一个结果集中指定Order by子句改变排序方式。 

		例如： 

		select employee_id,job_id from employees 
		union 
		select employee_id,job_id from job_history 

		以上将两个表的结果联合在一起。这两个例子会将两个select语句的结果中的重复值进行压缩，也就是结果的数据并不是两条结果的条数的和。如果希望即使重复的结果显示出来可以使用union all,例如： 

		2.在oracle的scott用户中有表emp 
		select * from emp where deptno >= 20 
		union all 
		select * from emp where deptno <= 30 
		这里的结果就有很多重复值了。 

		有关union和union all关键字需要注意的问题是： 

		union 和 union all都可以将多个结果集合并，而不仅仅是两个，你可以将多个结果集串起来。 
		使用union和union all必须保证各个select 集合的结果有相同个数的列，并且每个列的类型是一样的。但列名则不一定需要相同，oracle会将第一个结果的列名作为结果集的列名。例如下面是一个例子： 
		select empno,ename from emp 
		union 
		select deptno,dname from dept 
		我们没有必要在每一个select结果集中使用order by子句来进行排序，我们可以在最后使用一条order by来对整个结果进行排序。例如： 
		select empno,ename from emp 
		union 
		select deptno,dname from dept 
		order by ename;

	b.  多用连接查询来代替子查询
		因为子查询时，mysql需要为内层查询结果建立一个临时表，然后外层查询在临时表中查找，查询完后需要撤销临时表。
		而连接查询不需要建立临时表，所以比子查询快。

	c.  优化insert语句
			1.
			insert into 表名 values 
			(......),
			(......);
			2.
			insert into 表名 values (......);
			insert into 表名 values (......);

			上面两种插入方法，第一种与数据库的连接等操作，明显比第二种快，因为第一种只需要做一次连接，而第二种每一条要单独做一次连接。

	d.  常用命令
			MYSQL系统命令:
				1):查看当前有哪些数据库
				  show databases;
				(2):使用mysql数据库
				 use test；
				(3):查看当前数据库下的表
				  show tables;
				(4):查看上述grade表建立的命令
				show  create table grade;
				(5):查看student表的结构
				desc student;
				(6):查看数据库支持的存储引擎
				show engines; 
				show engines \G ;      //  \G让结果更美观
				(7):查看默认存储引擎
				show variables like 'storage_engine';
			表操作：
				(1)将grade表的course字段的数据类型修改为varchar（20）

				alter table grade modify course varchar(20);

				(2)将s_num字段的位置改到course前面

				alter table grade modify  s_num  int(10) after id;

				(3)将grade字段改名为score

				alter table grade change grade score varchar(10);

				(4)删除grade的外键约束
				alter table grade drop foreign key grade_fk;

				(5)将grade的存储引擎修改为INnoDB

				alter grade engine=INnoDB；

				(6)将student的address字段删除
				alter table student drop address;

				(7)在student表中增加名位phone的字段
				alter table student add phone int (10);

				(8)将grade的表名修改为gradeinfo
				alter table grade rename gradeinfo;

				(9):删除student表
				drop table student; 

5.一些优化建议--可能与前面一些地方重复
	a.
		1：索引可以大幅度提高查询性能
			1.1 缺省情况下建立的索引是非群集索引，但有时它并不是最佳的。在非群集索引下，数据在物理上随机存放在数据页上。合理的索引设计要建立在对各种查		询的分析和预测上。一般来说：

				a.有大量重复值、且经常有范围查询( > ,< ，> =,< =)和order by、group by发生的列，可考虑建立群集索引;

				b.经常同时存取多列，且每列都含有重复值可考虑建立组合索引;

				c.组合索引要尽量使关键查询形成索引覆盖，其前导列一定是使用最频繁的列。索引虽有助于提高性能但不是索引越多越好，恰好相反过多的索引会导致系统低效。用户在表中每加进一个索引，维护索引集合就要做相应的更新工作。

			1.2 用explain语句查询索引使用情况 

				索引注意事项：
				a. 使用FULLTEXT参数可以设置索引为全文索引，全文索引只能创建在CHAR ,VARCHAR ,TEXT类型字段上。->>但只有MyISAM存储引擎支持全文索引。

				b: 多列索引：在表的多列字段上建立一个索引，但只有遵循最左原则的时候，索引才会被使用。

				c. 查询语句使用like关键字进行查询，如果匹配的第一个字符为”%“时，索引不会被使用
				   
				d. 查询语句中使用or关键字时，只有or前后两个条件的列都是索引时，查询时才使用索引

				e. 最好在相同类型的字段间进行比较，如不能将建有索引的int字段与bigint字段进行比较

					如在一个DATE类型的字段上使用YEAR()函数时，将会使索引不能发挥应有的作用。所以，下面的两个查询虽然返回的结果一样，但后者要比前者快得多。
					SELECT * FROM order WHERE YEAR(OrderDate)<2001;
					SELECT * FROM order WHEREOrderDate<"2001-01-01";

				f. 任何对列的操作都将导致表扫描，它包括数据库函数、计算表达式等等，查询时要尽可能将操作移至等号右边

				        因为在WHERE子句中对字段进行函数或表达式操作，这将导致引擎放弃使用索引而进行全表扫描

				如：SELECT * FROM inventory WHERE Amount/7<24;
				SELECT * FROM inventory WHERE Amount<24*7;
				上面的两个查询也是返回相同的结果，但后面的查询将比前面的一个快很多

				SELECT * FROM RECORD WHERESUBSTRING(CARD_NO,1,4)=’5378’
				应改为: SELECT *FROM RECORD WHERE CARD_NO LIKE ‘5378%’

				g. 搜索字符型字段时，我们有时会使用LIKE 关键字和通配符，这种做法虽然简单，但却也是以牺牲系统性能为代价的
				例如下面的查询将会比较表中的每一条记录。
				SELECT * FROM books WHERE name like "MySQL%"
				但是如果换用下面的查询，返回的结果一样，但速度就要快上很多：
				SELECT * FROM books WHERE name>="MySQL"andname<"MySQM";

				h. 避免使用!=或＜＞、IS NULL或IS NOT NULL、IN ，NOT IN等这样的操作符,因为这会使系统无法使用索引,而只能直接搜索表中的数据。

				例如:  SELECT id FROM employee WHERE id !="B%" 优化器将无法通过索引来确定将要命中的行数,因此需要搜索该表的所有行。


				i.  能够用BETWEEN的就不要用IN,因为IN会使系统无法使用索引,而只能直接搜索表中的数据

				 如：SELECT * FROM T1 WHERE ID IN(10,11,12,13,14)改成：SELECT *FROM T1 WHERE ID BETWEEN 10 AND 14

		2：在可能的情况下尽量限制尽量结果集行数

			2.1:使用top
			如：SELECT TOP 300 COL1,COL2,COL3 FROM T

			2.2:增加 limit 1 会让查询更加有效
			      这样数据库引擎发现只有1后停止扫描，而不会去扫描整个表或索引

			2.3:尽量避免select * 命令，而是需要什么字段，查询什么字段

		3： 合理使用EXISTS,NOT EXISTS子句

			如果想校验表里是否存在某条纪录，不要用count(*)那样效率很低，而且浪费服务器资源。可以用EXISTS代替。如：

			IF (SELECT COUNT(*) FROM table_name WHEREcolumn_name = 'xxx')
			可以写成：
			IF EXISTS (SELECT * FROM table_name WHERE column_name = 'xxx')

		4：数据类型

			4.1:  	只要能满足需求，应尽可能使用小的数据类型，
			   		比如能用tinyint 就不用int

			4.2:  	varchar比char节省空间，但效率比char低，想要获得效率就得牺牲一定空间。
					如果一个varchar的列经常被修改，而且修改的数据长度不同，会引起‘行迁移’问题，造成多余I/O花费，这时最好用char代替varchar
					如果是像身份证定长的字段，一定要用char ,查询时是全字段匹配，能获取更高效率。
					使用varchar(5)和varchar(200)保存‘hello’占用的空间都是一样的，但是使用较短的列有巨大优势，因为较大的列会占用更多的
					内存。

			4.3:  	如果字段类型只有少量的几个，最好使用enum类型，因为enum类型被当作数值型数据来处理，而数值型数据被处理起来的速度要比文本类型快得多.
					例如省份，性别等字段。

			4.4: 	尽量使用数字型字段，一部分开发人员和数据库管理人员喜欢把包含数值信息的字段设计为字符型，这会降低查询和连接的性能，并会增加存储开销。
					这是因为引擎在处理查询和连接回逐个比较字符串中每一个字符，而对于数字型而言只需要比较一次就够了

			4.5：	一般情况下日期和时间类型最好选择timestamp，因为datetime占用8字节存储空间，而timestamp占用4字节存储空间，明显更节约空间。

		5. 	在可能的情况下，应该尽量把字段设置为NOT NULL，这样在将来执行查询的时候，数据库不用去比较NULL值。

		 	空值代表未知的状态。因此，当包含空值的列参与运算时，结果是不可知的。在表定义的过程中，可以指定必须提供一个有效值，这可通过在列定义中增加一个短语来实现。CREATE TABLE语句可以在每个列定义之后添加短语NOT NULL，这将保证该列包含确实的数据值，即非空。

			  在编制DB2的应用程序时，往往要对恰当使用空值进行特殊的考虑。DB2对空值的处理与对具体值的处理方式是不同的。

			  注意：关系型数据库允许空值。重要的是要记住，这些空值在你的数据库设计中是否恰当。

			  如果要定义一个不允许空值的列，可以通过在列定义的最后加上短语NOT NULL来实现，例如：

			  CREATE TABLE t1 (c1 CHAR(3) NOT NULL)

			  在上面的例子中，DB2将不允许在c1列中存储任何空值。一般来说，除非是数据库设计的需要，应该避免使用允许空值的列。还必须考虑存储空间的额外开销。如果允许空值，每个列都将多占用一个字节。

			  如果在表中插入一行时省略了一个或多个列的值，那么这些列或者将或者被置为空值（如果这个列允许空值），或者被定义成默认值（如果曾经定义过）。如果这个列被定义成不允许空值，除非给该列提供有效的值，否则插入操作将失败。DB2对于每一种DB2的数据类型都定义了默认值，但是你可以为每一列提供默认值。默认值在CREATE TABLE语句中定义。通过定义自己的默认值，可以保证数据值被自动置为已知的值。

			  注意：DB2的默认值可能不是你想要的！可在CREATE TABLE语句中定义默认值。

			  要保证在进行INSERT操作时使用了默认值，必须保证在INSERT语句的VALUE部分给出DEFAULT关键字。

		6. 	使用连接查询(join)代替子查询
			因为子查询时，mysql需要为内层查询结果建立一个临时表，然后外层查询在临时表中查找，查询完后需要撤销临时表。
			而连接查询不需要建立临时表，所以比子查询快。

		7. 	使用联合(union)来代替手动创建的临时表

			MySQL 从 4.0 的版本开始支持UNION 查询，它可以把需要使用临时表的两条或更多的 SELECT 查询合并的一个查询中。在客户端的查询会话结束的时候，临时表会被自
			动删除，从而保证数据库整齐、高效。使用 UNION 来创建查询的时候，我们只需要用 UNION作为关键字把多个 SELECT 语句连接起来就可以了，要注意的是所有
			SELECT语句中的字段数目要想同。下面的例子就演示了一个使用 UNION的查询。
			select  name,phone from client  union  select name,birthdate from author union select name,supplier from product

		8. 	充分利用连接条件

			在某种情况下，两个表之间不止一个连接条件，这时可在where子句中将连接条件完整写上，可大大提高查询速度。如：
			SELECT SUM(A.AMOUNT) FROM ACCOUNT A,CARD B	WHERE A.CARD_NO = B.CARD_NO 
			SELECT SUM(A.AMOUNT) FROM ACCOUNT A,CARD B	WHERE A.CARD_NO = B.CARD_NO AND A.ACCOUNT_NO=B.ACCOUNT_NO
			第二句将比第一句执行快得多。

		9. 	能用DISTINCT的就不用GROUP BY

			SELECT OrderID FROM Details WHERE UnitPrice > 10 GROUP BY OrderID 
			可改为：
			SELECT DISTINCT OrderID FROM Details WHERE UnitPrice > 10

		10. 尽量不要用SELECT INTO语句。 SELECTINTO 语句会导致表锁定，阻止其他用户访问该表

		11. UPDATE语句建议：
			a. 尽量不要修改主键字段。
			b. 当修改VARCHAR型字段时，尽量使用相同长度内容的值代替。
			c. 尽量最小化对于含有UPDATE触发器的表的UPDATE操作。
			d. 避免UPDATE将要复制到其他数据库的列。
			e. 避免UPDATE建有很多索引的列。
			f. 避免UPDATE在WHERE子句条件中的列。

		12. 固定长度的表会更快

			如果表中的所有字段都是“固定长度”的，整个表会被认为是 “static” 或 “fixed-length”。 例如，表中没有如下类型的字段： VARCHAR，TEXT，BLOB。只要你包括了其中一个这些字段，那么这个表就不是“固定长度静态表”了，这样，MySQL 引擎会用另一种方法来处理。

			固定长度的表会提高性能，因为MySQL搜寻得会更快一些，因为这些固定的长度是很容易计算下一个数据的偏移量的，所以读取的自然也会很快。而如果字段不是定长的，那么，每一次要找下一条的话，需要程序找到主键。

			并且，固定长度的表也更容易被缓存和重建。不过，唯一的副作用是，固定长度的字段会浪费一些空间，因为定长的字段无论你用不用，他都是要分配那么多的空间。

			使用“垂直分割”技术（见下一条），你可以分割你的表成为两个一个是定长的，一个则是不定长的。

			垂直分割

			“垂直分割”是一种把数据库中的表按列变成几张表的方法，这样可以降低表的复杂度和字段的数目，从而达到优化的目的。

			示例一：在Users表中有一个字段是家庭地址，这个字段是可选字段，相比起，而且你在数据库操作的时候除了个人信息外，你并不需要经常读取或是改写这个字段。那么，为什么不把他放到另外一张表中呢？ 这样会让你的表有更好的性能，大家想想是不是，大量的时候，我对于用户表来说，只有用户ID，用户名，口令，用户角色等会被经常使用。小一点的表总是会有好的性能。

			示例二： 你有一个叫 “last_login” 的字段，它会在每次用户登录时被更新。但是，每次更新时会导致该表的查询缓存被清空。所以，你可以把这个字段放到另一个表中，这样就不会影响你对用户ID，用户名，用户角色的不停地读取了，因为查询缓存会帮你增加很多性能。

			另外，你需要注意的是，这些被分出去的字段所形成的表，你不会经常性地去Join他们，不然的话，这样的性能会比不分割时还要差，而且，会是极数级的下降。
		
		13. 拆分大的 DELETE 或 INSERT 语句

			如果你需要在一个在线的网站上去执行一个大的 DELETE 或 INSERT 查询，你需要非常小心，要避免你的操作让你的整个网站停止相应。因为这两个操作是会锁表的，表一锁住了，别的操作都进不来了。

			Apache 会有很多的子进程或线程。所以，其工作起来相当有效率，而我们的服务器也不希望有太多的子进程，线程和数据库链接，这是极大的占服务器资源的事情，尤其是内存。

			如果你把你的表锁上一段时间，比如30秒钟，那么对于一个有很高访问量的站点来说，这30秒所积累的访问进程线程，数据库链接，打开的文件数，可能不仅仅会让你泊WEB服务Crash，还可能会让你的整台服务器马上掛了。

		14. 越小的列会越快

			对于大多数的数据库引擎来说，硬盘操作可能是最重大的瓶颈。所以，把你的数据变得紧凑会对这种情况非常有帮助，因为这减少了对硬盘的访问。

			参看 MySQL 的文档 Storage Requirements 查看所有的数据类型。

			如果一个表只会有几列罢了（比如说字典表，配置表），那么，我们就没有理由使用 INT 来做主键，使用 MEDIUMINT, SMALLINT 或是更小的 TINYINT 会更经济一些。如果你不需要记录时间，使用 DATE 要比 DATETIME 好得多。

			当然，你也需要留够足够的扩展空间。

		15. 选择正确的存储引擎

			在 MySQL 中有两个存储引擎 MyISAM 和 InnoDB，每个引擎都有利有弊。

			MyISAM 适合于一些需要大量查询的应用，但其对于有大量写操作并不是很好。甚至你只是需要update一个字段，整个表都会被锁起来，而别的进程，就算是读进程都无法操作直到读操作完成。另外，MyISAM 对于 SELECT COUNT(*) 这类的计算是超快无比的。

			InnoDB 的趋势会是一个非常复杂的存储引擎，对于一些小的应用，它会比 MyISAM 还慢。他是它支持“行锁” ，于是在写操作比较多的时候，会更优秀。并且，他还支持更多的高级应用，比如：事务。

		16. 为查询缓存优化你的查询

			大多数的MySQL服务器都开启了查询缓存。这是提高性最有效的方法之一，而且这是被MySQL的数据库引擎处理的。当有很多相同的查询被执行了多次的时候，这些查询结果会被放到一个缓存中，这样，后续的相同的查询就不用操作表而直接访问缓存结果了。

			这里最主要的问题是，对于程序员来说，这个事情是很容易被忽略的。因为，我们某些查询语句会让MySQL不使用缓存。请看下面的示例：

			// 查询缓存不开启
			$r = mysql_query("SELECT username FROM user WHERE signup_date >= CURDATE()");

			// 开启查询缓存
			$today = date("Y-m-d");
			$r = mysql_query("SELECT username FROM user WHERE signup_date >= '$today'");

			上面两条SQL语句的差别就是 CURDATE() ，MySQL的查询缓存对这个函数不起作用。所以，像 NOW() 和 RAND() 或是其它的诸如此类的SQL函数都不会开启查询缓存，因为这些函数的返回是会不定的易变的。所以，你所需要的就是用一个变量来代替MySQL的函数，从而开启缓存。

		17. 当只要一行数据时使用 LIMIT 1

			当你查询表的有些时候，你已经知道结果只会有一条结果，但因为你可能需要去fetch游标，或是你也许会去检查返回的记录数。

			在这种情况下，加上 LIMIT 1 可以增加性能。这样一样，MySQL数据库引擎会在找到一条数据后停止搜索，而不是继续往后查少下一条符合记录的数据。

			在查询的数据集很大的情况下，加上limit1会提高效率


Eastblue可用语句
	查询夜夜三国的各等级铜钱消耗情况
	
	select level,sum(diff_mana) from 
	(select diff_mana,max(ifnull(ll.lev, 1)) as level 
		from `72.1`.log_economy le left join `72.1`.log_levelup ll on le.player_id = ll.player_id and ll.created_at < le.created_at 
		where diff_mana < 0 group by le.id) 
	b group by b.level

	查询夜夜三国时间段内玩家充值排行以及各种玩家信息

	select tmp.*, max(ll.lev) as first_pay_level, from_unixtime(tmp.first_pay_time)  from 
	(select o.pay_user_id as uid, u.nickname, u.created_ip,from_unixtime(lcp.created_time) as created_time, lcp.player_id, name.player_name, level.level_now, sum(o.pay_amount*exchange) as dol, (unix_timestamp(now()) - max(o.pay_time))/86400 as unpay_time, count(1) as pay_time, min(pay_time) as first_pay_time 
		from `payment_55`.pay_order o 
		join users u on o.pay_user_id=u.uid 
		left join `72.1`.log_create_player lcp on o.pay_user_id = lcp.uid 
		join 
		(select idn.player_id,idn.player_name from 
			(select lpn.player_id,lpn.player_name from `72.1`.log_player_name as lpn ORDER BY created_at desc) 
			as idn GROUP BY idn.player_id) 
		as name on name.player_id=lcp.player_id 
		join 
		(select player_id,max(lev) as level_now from `72.1`.log_levelup GROUP BY player_id) 
		as level on lcp.player_id=level.player_id 
		where o.get_payment = 1 and o.pay_time between unix_timestamp('2015-11-01') and unix_timestamp('2015-12-01') and o.game_id = 72 
		group by o.pay_user_id order by dol desc limit 500) 
	tmp 
	join `72.1`.log_levelup ll on tmp.player_id = ll.player_id and ll.created_at <= tmp.first_pay_time 
	group by ll.player_id order by tmp.dol desc

	查询夜夜三国某个操作的玩家数量以及分别花费铜钱和元宝的次数

	select lcp.player_id,lpn.player_name,ifnull(a.times,0) as manatimes, ifnull(b.times,0) as crystaltimes 
	from `72.1`.log_create_player lcp 
	left join 
	(select player_id,count(1) as times 
		from `72.1`.log_economy where mid = 785 and diff_mana < 0 group by player_id) 
	a on lcp.player_id = a.player_id 
	left join
	(select player_id,count(1) as times from `72.1`.log_economy where mid = 785 and diff_crystal < 0 group by player_id) 
	b on lcp.player_id = b.player_id 
	join 
	(select * from 
		(select player_id,player_name from `72.1`.log_player_name order by created_at desc) a group by a.player_id) 
	lpn on lcp.player_id=lpn.player_id having manatimes+crystaltimes > 0 order by (manatimes+crystaltimes) desc

	查询夜夜三国一段时间内进行了某个操作的玩家及玩家在这个操作上花费的铜钱和元宝的次数 by zsl
	select e.player_id,pn.player_name,e.manatimes,e.crystaltimes from 
	(select player_id,sum(if(diff_mana<0,1,0)) as manatimes,sum(if(diff_crystal<0,1,0)) as crystaltimes from `54.1`.log_economy where mid=785 and created_at between unix_timestamp('2015-11-27') and unix_timestamp('2015-11-28')  and diff_crystal+diff_mana<0 group by player_id) e 
	left join `54.1`.log_create_player p on e.player_id=p.player_id 
	join (select player_name,player_id from (select player_id,player_name from `54.1`.log_player_name order by id desc) t group by player_id) pn on p.player_id=pn.player_id  

	萌娘三国查询最后登录时间早于某个时间点的总充值超过某个值的玩家 
	
	select a.pay_user_id, a.dollar, a.last_visit_time, sl.server_name,cp.player_id,cp.server_id as server_internal_id from (select o.pay_user_id,o.server_id,sum(pay_amount*exchange) as dollar,u.last_visit_time from `payment`.pay_order o join users u on o.pay_user_id = u.uid and o.game_id = 66 and o.get_payment = 1 and o.pay_time between unix_timestamp('2015-08-25 00:00:00') and unix_timestamp('2015-12-20 00:00:00') and u.last_visit_time < '2015-12-18 00:00:00' group by o.pay_user_id having dollar > 150) a join server_list sl on a.server_id = sl.server_id left join create_player cp on cp.game_id = 66 and cp.server_id = sl.server_internal_id and cp.uid = a.pay_user_id

	萌娘三国查询玩家最后登录时间早于某个时间点充值超过某个值的玩家信息 by zsl
	select sl.server_name,o.pay_user_id as uid,l.player_id,round(sum(pay_amount*exchange),2) as dollar,from_unixtime(l.last_time) as last_time from 
	(select player_id,max(action_time) as last_time from `66.1`.log_login group by player_id having last_time<unix_timestamp('2016-05-01')) l
	join `66.1`.log_create_player p on p.player_id=l.player_id
	join `payment`.pay_order o on o.pay_user_id=p.uid
	join `payment`.server_list sl on o.server_id= sl.server_id 
	where sl.server_internal_id=1 and o.game_id=66 and sl.game_id=66 and get_payment=1
	group by o.pay_user_id having dollar>1000 order by dollar desc

	夜夜三国查询充值玩家的相关信息
	select a.*,from_unixtime(max(ll.action_time)) from (select lcp.player_id,u.device_id,from_unixtime(lcp.created_time),lcp.created_ip,sum(pay_amount*exchange) as dollar from `payment_29`.pay_order o join `69.1`.log_create_player lcp on o.pay_user_id = lcp.uid and o.game_id = 69 and o.get_payment = 1 and o.pay_time between unix_timestamp('2015-08-01 00:00:00') and unix_timestamp('2015-12-20 00:00:00') join qiqiwu_29.users u on lcp.uid = u.uid group by o.pay_user_id) a join `69.1`.log_login ll on a.player_id = ll.player_id group by a.player_id

	夜夜三国查询某个时间点之后充值超过某个值的玩家

	select lcp.player_id,a.pay_user_id as uid,a.pay_dollar from (select o.pay_user_id,sum(pay_amount*exchange) as pay_dollar from payment_29.pay_order o where o.game_id = 69 and o.get_payment = 1 and o.pay_time > unix_timestamp('2015-10-01 00:00:00') group by o.pay_user_id having pay_dollar > 300 order by pay_dollar desc) a left join `69.1`.log_create_player lcp on a.pay_user_id = lcp.uid

	夜夜三国查询单月充值超过某个值的玩家创建和最后登录信息

	select lcp.player_id,lcp.created_ip as '创建IP',from_unixtime(lcp.created_time) as '创建时间',a.*,from_unixtime(max(ll.action_time)) as '最后登录时间',max(ll.lev) as '最后登录等级' from (select o.pay_user_id,sum(o.pay_amount*exchange) as dollar,count(1) as pay_times from pay_order o where o.game_id = 72 and o.get_payment = 1 and o.pay_time between unix_timestamp('2015-12-01 00:00:00') and unix_timestamp('2016-01-01 00:00:00') group by o.pay_user_id having dollar>300) a join `72.1`.log_create_player lcp on a.pay_user_id = lcp.uid join `72.1`.log_login ll on lcp.player_id = ll.player_id group by lcp.player_id

	夜夜三国查询某个时间点后创建，达到25级，且最近三天有登陆的玩家达到5,10,15,20,25级的时间以及玩家的创建时间
	select a.player_id,from_unixtime(a.created_time), lp.lev, from_unixtime(lp.created_at) from (select distinct lcp.player_id,lcp.created_time from `54.1`.log_create_player lcp join `54.1`.log_login ll on lcp.player_id = ll.player_id and lcp.created_time > unix_timestamp('2016-01-06 00:00:00') and ll.action_time > unix_timestamp(now())-3*86400 join `54.1`.log_levelup lp on lcp.player_id = lp.player_id where lp.lev = 25) a join `54.1`.log_levelup lp on a.player_id = lp.player_id and lp.lev in (5,10,15,20,25) order by a.created_time

	夜夜三国查询某个时间点后创建，达到25级，且最近三天有登陆的玩家达到5,10,15,20,25级的时间以及玩家的创建时间 (和上一个不同的是，每个玩家的几个升级时间在同一行) --by zsl:
	select plv.player_id,from_unixtime(plv.created_time),v1.lev,from_unixtime(v1.created_at), v2.lev,from_unixtime(v2.created_at),v3.lev,from_unixtime(v3.created_at) from 
	(select distinct p.created_time,p.player_id from `75.1`.log_create_player p 
	join `75.1`.log_levelup lv  on p.player_id=lv.player_id and p.created_time>unix_timestamp('2016-04-01') and  lv.lev =25
	join `75.1`.log_login lg on  lv.player_id=lg.player_id  and  lg.action_time>unix_timestamp(now())-3*86400 ) plv 
	join `75.1`.log_levelup v1 on plv.player_id=v1.player_id and v1.lev=15
	join `75.1`.log_levelup v2 on plv.player_id=v2.player_id and v2.lev=20
	join `75.1`.log_levelup v3 on plv.player_id=v3.player_id and v3.lev=25
	order by plv.player_id

	夜夜三国查询某个时间点后创建，达到25级，且最近三天有登陆的玩家达到5,10,15,20,25级的时间以及玩家的创建时间 by zsl(和上一个不同的是，可以查出当前等级)
	select plv.player_id,from_unixtime(plv.created_time),plv.now_lev,from_unixtime(v1.created_at), from_unixtime(v2.created_at),from_unixtime(v3.created_at) from 
	(select distinct p.created_time,p.player_id,max(lv.lev) as now_lev from `75.1`.log_create_player p 
	join `75.1`.log_levelup lv  on p.player_id=lv.player_id and p.created_time>unix_timestamp('2016-04-01') and  lv.lev >=25
	join `75.1`.log_login lg on  lv.player_id=lg.player_id  and  lg.action_time>unix_timestamp(now())-3*86400 group by p.player_id) plv 
	join `75.1`.log_levelup v1 on plv.player_id=v1.player_id and v1.lev=15
	join `75.1`.log_levelup v2 on plv.player_id=v2.player_id and v2.lev=20
	join `75.1`.log_levelup v3 on plv.player_id=v3.player_id and v3.lev=25
	 order by plv.player_id

	夜夜三国查询某个时间点后创建，达到25级的玩家达到5,10,15,20,25级的时间以及玩家的创建时间
	select a.player_id,from_unixtime(a.created_time), lp.lev, from_unixtime(lp.created_at) from (select lcp.player_id,lcp.created_time from `54.1`.log_create_player lcp join `54.1`.log_levelup ll on lcp.player_id = ll.player_id and ll.lev = 25 and lcp.created_time > unix_timestamp('2016-01-06 00:00:00')) a join `54.1`.log_levelup lp on a.player_id = lp.player_id and lp.lev in (5,10,15,20,25) 

	查询渠道留存
	select b.source,b.create_date, a.create_player_num,b.login_date,b.login_num,concat((b.login_num/a.create_player_num)*100, '%') as rate  from (select from_unixtime(lcp.created_time, "%Y-%m-%d") as create_date,u.source,count(distinct player_id) as create_player_num from `81.5`.log_create_player lcp join `qiqiwu_59`.users u on lcp.uid = u.uid group by create_date,u.source) a join (select from_unixtime(lcp.created_time, "%Y-%m-%d") as create_date,from_unixtime(ll.action_time, "%Y-%m-%d") as login_date,u.source,count(distinct ll.player_id) as login_num  from `81.5`.log_create_player lcp join `qiqiwu_59`.users u on lcp.uid = u.uid join `81.5`.log_login ll on lcp.player_id = ll.player_id group by create_date, login_date, u.source order by create_date) b on a.create_date = b.create_date and a.source = b.source where b.create_date > '2016-03-22' order by b.create_date,b.source,b.login_date

	select [value] as server_id,from_unixtime(ll.action_time, "%Y-%m-%d") as date, count(distinct ll.player_id) from `66.[value]`.log_create_player lcp join payment.pay_order o on lcp.uid = o.pay_user_id and o.game_id = 66 and o.get_payment = 1 join `66.[value]`.log_login ll on lcp.player_id = ll.player_id and ll.action_time > unix_timestamp('2015-12-01 00:00:00') group by date


	select d.date,count(distinct d.player_id) from (select c.player_id,c.date,max(ll.action_time) as last_login_time from (select a.player_id,b.date from (select distinct lcp.player_id from `66.1`.log_create_player lcp join payment.pay_order o on lcp.uid = o.pay_user_id and o.get_payment = 1 and o.game_id = 66 join qiqiwu.server_list sl on o.server_id = sl.server_id and sl.server_internal_id = 1) a join (select distinct from_unixtime(created_time, "%Y-%m-%d") as date from `66.1`.log_create_player lcp where lcp.created_time > unix_timestamp('2015-12-01 00:00:00')) b) c join `66.1`.log_login ll on c.player_id = ll.player_id and ll.action_time < unix_timestamp(c.date) group by c.player_id,c.date) d where d.last_login_time < unix_timestamp(d.date)-7*86400 group by d.date

	萌娘查询各项操作的钻石消耗，为了把这些结果列到一列上，不能单纯groupby(mid)，这样的结果更友好
	select [value] as server_internal_id, from_unixtime(created_at, "%Y-%m-%d") as date, sum(if(mid=97, diff_crystal, 0)) as '购买体力',
	sum(if(mid=96, diff_crystal, 0)) as '购买金币',
	sum(if(mid=116, diff_crystal, 0)) as '重置精英副',
	sum(if(mid=117, diff_crystal, 0)) as '扫荡 ',
	sum(if(mid=23, diff_crystal, 0)) as '改名字 ',
	sum(if(mid=29, diff_crystal, 0)) as '每日消费',
	sum(if(mid=33, diff_crystal, 0)) as '商店购买',
	sum(if(mid=34, diff_crystal, 0)) as '刷新商店',
	sum(if(mid=50, diff_crystal, 0)) as '召唤战姬',
	sum(if(mid=99, diff_crystal, 0)) as '购买技能点',
	sum(if(mid=125, diff_crystal, 0)) as '洗练 ',
	sum(if(mid=127, diff_crystal, 0)) as '自动洗练',
	sum(if(mid=231, diff_crystal, 0)) as '购买活动礼',
	sum(if(mid=232, diff_crystal, 0)) as '购买基金',
	sum(if(mid=248, diff_crystal, 0)) as '购买风筝节挑战',
	sum(if(mid=281, diff_crystal, 0)) as '购买竞技场次',
	sum(if(mid=288, diff_crystal, 0)) as '重置竞技场C',
	sum(if(mid=341, diff_crystal, 0)) as '全国制霸宝',
	sum(if(mid=529, diff_crystal, 0)) as '购买打工面',
	sum(if(mid=564, diff_crystal, 0)) as '购买斗牛次',
	sum(if(mid=565, diff_crystal, 0)) as '重置斗牛CD',
	sum(if(mid=577, diff_crystal, 0)) as '创建社团',
	sum(if(mid=591, diff_crystal, 0)) as '购买社团红',
	sum(if(mid=625, diff_crystal, 0)) as '购买元素boss',
	sum(if(mid=630, diff_crystal, 0)) as '购买年兽挑战',
	sum(if(mid=656, diff_crystal, 0)) as '购买头像' 
	 from `66.[value]`.log_economy where diff_crystal < 0 and created_at > unix_timestamp('2015-12-01 00:00:00') group by date

 英文学妹查询菲律宾用户的付费情况，大量的部分都是判断ip的
 select from_unixtime(pay_time, "%Y-%m-%d") as date,o.pay_type_id, pt.pay_type_name,count(distinct pay_user_id) as pay_user_num, count(distinct order_id) as pay_order_num, sum(pay_amount*exchange) as pay_dollar from (select distinct u.uid from create_player cp join users u on cp.uid = u.uid and cp.game_id = 79 and (INET_ATON(last_visit_ip) between INET_ATON('1.37.0.0') and INET_ATON('1.37.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('14.192.0.0') and INET_ATON('14.192.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.50.0.0') and INET_ATON('27.50.3.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.106.216.0') and INET_ATON('27.106.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.108.0.0') and INET_ATON('27.108.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.109.64.0') and INET_ATON('27.109.95.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.110.128.0') and INET_ATON('27.110.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.123.224.0') and INET_ATON('27.123.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.124.64.0') and INET_ATON('27.124.79.255')
or INET_ATON(last_visit_ip) between INET_ATON('27.126.152.0') and INET_ATON('27.126.155.255')
or INET_ATON(last_visit_ip) between INET_ATON('49.144.0.0') and INET_ATON('49.151.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('49.157.0.0') and INET_ATON('49.157.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('58.69.0.0') and INET_ATON('58.69.56.255')
or INET_ATON(last_visit_ip) between INET_ATON('58.69.57.0') and INET_ATON('58.69.57.255')
or INET_ATON(last_visit_ip) between INET_ATON('58.69.58.0') and INET_ATON('58.69.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('58.71.0.0') and INET_ATON('58.71.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('61.9.0.0') and INET_ATON('61.9.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('61.14.192.0') and INET_ATON('61.14.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('61.28.128.0') and INET_ATON('61.28.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('101.78.16.0') and INET_ATON('101.78.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.1.116.0') and INET_ATON('103.1.119.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.3.80.0') and INET_ATON('103.3.83.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.4.20.0') and INET_ATON('103.4.23.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.5.0.0') and INET_ATON('103.5.7.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.5.60.0') and INET_ATON('103.5.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.6.96.0') and INET_ATON('103.6.99.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.6.181.0') and INET_ATON('103.6.181.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.6.248.0') and INET_ATON('103.6.251.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.7.146.0') and INET_ATON('103.7.146.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.7.224.0') and INET_ATON('103.7.224.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.10.152.0') and INET_ATON('103.10.155.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.10.176.0') and INET_ATON('103.10.179.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.10.200.0') and INET_ATON('103.10.203.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.10.254.0') and INET_ATON('103.10.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.11.40.0') and INET_ATON('103.11.43.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.11.64.0') and INET_ATON('103.11.67.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.11.112.0') and INET_ATON('103.11.115.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.12.88.0') and INET_ATON('103.12.91.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.13.32.0') and INET_ATON('103.13.35.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.13.134.0') and INET_ATON('103.13.134.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.14.56.0') and INET_ATON('103.14.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.14.104.0') and INET_ATON('103.14.107.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.14.194.0') and INET_ATON('103.14.194.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.15.252.0') and INET_ATON('103.15.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.16.168.0') and INET_ATON('103.16.171.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.17.20.0') and INET_ATON('103.17.23.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.17.248.0') and INET_ATON('103.17.248.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.19.16.0') and INET_ATON('103.19.16.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.19.32.0') and INET_ATON('103.19.35.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.21.12.0') and INET_ATON('103.21.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.21.168.0') and INET_ATON('103.21.171.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.23.96.0') and INET_ATON('103.23.99.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.23.174.0') and INET_ATON('103.23.174.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.24.16.0') and INET_ATON('103.24.19.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.25.176.0') and INET_ATON('103.25.179.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.25.198.0') and INET_ATON('103.25.199.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.26.36.0') and INET_ATON('103.26.39.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.27.122.0') and INET_ATON('103.27.123.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.28.16.0') and INET_ATON('103.28.19.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.28.68.0') and INET_ATON('103.28.71.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.28.202.0') and INET_ATON('103.28.203.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.29.20.0') and INET_ATON('103.29.23.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.29.80.0') and INET_ATON('103.29.83.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.29.244.0') and INET_ATON('103.29.247.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.29.250.0') and INET_ATON('103.29.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.244.28.0') and INET_ATON('103.244.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.246.20.0') and INET_ATON('103.246.23.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.246.172.0') and INET_ATON('103.246.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.247.38.0') and INET_ATON('103.247.39.255')
or INET_ATON(last_visit_ip) between INET_ATON('103.247.252.0') and INET_ATON('103.247.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.5.64.0') and INET_ATON('110.5.71.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.44.96.0') and INET_ATON('110.44.111.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.44.128.0') and INET_ATON('110.44.143.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.50.224.0') and INET_ATON('110.50.239.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.54.128.0') and INET_ATON('110.55.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.92.24.0') and INET_ATON('110.92.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.93.64.0') and INET_ATON('110.93.95.255')
or INET_ATON(last_visit_ip) between INET_ATON('110.232.160.0') and INET_ATON('110.232.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('111.68.32.0') and INET_ATON('111.68.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('111.125.64.0') and INET_ATON('111.125.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('111.223.0.0') and INET_ATON('111.223.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('111.235.80.0') and INET_ATON('111.235.95.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.78.48.0') and INET_ATON('112.78.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.109.0.0') and INET_ATON('112.109.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.196.224.0') and INET_ATON('112.196.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.198.0.0') and INET_ATON('112.198.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.199.0.0') and INET_ATON('112.199.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.200.0.0') and INET_ATON('112.200.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.201.0.0') and INET_ATON('112.201.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.202.0.0') and INET_ATON('112.202.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.203.0.0') and INET_ATON('112.205.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.206.0.0') and INET_ATON('112.206.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('112.207.0.0') and INET_ATON('112.211.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('113.20.144.0') and INET_ATON('113.20.151.255')
or INET_ATON(last_visit_ip) between INET_ATON('113.20.160.0') and INET_ATON('113.20.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('113.61.32.0') and INET_ATON('113.61.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('113.197.72.0') and INET_ATON('113.197.79.255')
or INET_ATON(last_visit_ip) between INET_ATON('113.212.176.0') and INET_ATON('113.212.183.255')
or INET_ATON(last_visit_ip) between INET_ATON('114.108.192.0') and INET_ATON('114.108.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('114.141.216.0') and INET_ATON('114.141.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('114.198.128.0') and INET_ATON('114.198.159.255')
or INET_ATON(last_visit_ip) between INET_ATON('115.31.64.0') and INET_ATON('115.31.79.255')
or INET_ATON(last_visit_ip) between INET_ATON('115.42.120.0') and INET_ATON('115.42.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('115.84.168.0') and INET_ATON('115.84.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('115.84.224.0') and INET_ATON('115.85.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('115.146.128.0') and INET_ATON('115.147.192.254')
or INET_ATON(last_visit_ip) between INET_ATON('115.147.192.255') and INET_ATON('115.147.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('116.50.128.0') and INET_ATON('116.50.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('116.66.248.0') and INET_ATON('116.66.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('116.68.176.0') and INET_ATON('116.68.183.255')
or INET_ATON(last_visit_ip) between INET_ATON('116.93.0.0') and INET_ATON('116.93.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('117.58.192.0') and INET_ATON('117.58.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('117.104.240.0') and INET_ATON('117.104.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('118.88.8.0') and INET_ATON('118.88.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('119.42.32.0') and INET_ATON('119.42.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('119.63.0.0') and INET_ATON('119.63.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('119.92.0.0') and INET_ATON('119.95.164.255')
or INET_ATON(last_visit_ip) between INET_ATON('119.95.165.0') and INET_ATON('119.95.165.255')
or INET_ATON(last_visit_ip) between INET_ATON('119.95.166.0') and INET_ATON('119.95.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('119.111.0.0') and INET_ATON('119.111.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('120.28.0.0') and INET_ATON('120.28.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('120.29.64.0') and INET_ATON('120.29.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('120.72.16.0') and INET_ATON('120.72.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('120.89.0.0') and INET_ATON('120.89.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('120.138.0.0') and INET_ATON('120.138.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('121.1.0.0') and INET_ATON('121.1.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('121.54.0.0') and INET_ATON('121.54.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('121.58.192.0') and INET_ATON('121.58.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('121.96.0.0') and INET_ATON('121.97.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('121.127.0.0') and INET_ATON('121.127.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.2.0.0') and INET_ATON('122.3.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.49.208.0') and INET_ATON('122.49.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.52.0.0') and INET_ATON('122.52.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.53.0.0') and INET_ATON('122.53.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.54.0.0') and INET_ATON('122.55.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.144.64.0') and INET_ATON('122.144.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('122.202.96.0') and INET_ATON('122.202.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('123.108.112.0') and INET_ATON('123.108.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('123.136.72.0') and INET_ATON('123.136.79.255')
or INET_ATON(last_visit_ip) between INET_ATON('123.176.64.0') and INET_ATON('123.176.71.255')
or INET_ATON(last_visit_ip) between INET_ATON('123.242.200.0') and INET_ATON('123.242.207.255')
or INET_ATON(last_visit_ip) between INET_ATON('124.6.128.0') and INET_ATON('124.6.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('124.19.128.0') and INET_ATON('124.19.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('124.83.0.0') and INET_ATON('124.83.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('124.104.0.0') and INET_ATON('124.107.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('124.158.32.0') and INET_ATON('124.158.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('124.217.0.0') and INET_ATON('124.217.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('125.5.0.0') and INET_ATON('125.5.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('125.60.128.0') and INET_ATON('125.60.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('125.212.0.0') and INET_ATON('125.212.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('165.220.0.0') and INET_ATON('165.220.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('175.158.192.0') and INET_ATON('175.158.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('175.176.0.0') and INET_ATON('175.176.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.94.0.0') and INET_ATON('180.94.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.94.36.0') and INET_ATON('180.94.39.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.190.0.0') and INET_ATON('180.190.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.191.0.0') and INET_ATON('180.191.255.254')
or INET_ATON(last_visit_ip) between INET_ATON('180.191.255.255') and INET_ATON('180.195.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.232.0.0') and INET_ATON('180.232.70.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.232.71.0') and INET_ATON('180.232.71.255')
or INET_ATON(last_visit_ip) between INET_ATON('180.232.72.0') and INET_ATON('180.232.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('182.16.128.0') and INET_ATON('182.16.139.255')
or INET_ATON(last_visit_ip) between INET_ATON('182.18.192.0') and INET_ATON('182.18.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('182.54.128.0') and INET_ATON('182.54.159.255')
or INET_ATON(last_visit_ip) between INET_ATON('182.237.28.0') and INET_ATON('182.237.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('182.239.32.0') and INET_ATON('182.239.39.255')
or INET_ATON(last_visit_ip) between INET_ATON('182.255.32.0') and INET_ATON('182.255.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('183.177.112.0') and INET_ATON('183.177.119.255')
or INET_ATON(last_visit_ip) between INET_ATON('183.182.64.0') and INET_ATON('183.182.79.255')
or INET_ATON(last_visit_ip) between INET_ATON('192.153.221.0') and INET_ATON('192.153.228.255')
or INET_ATON(last_visit_ip) between INET_ATON('192.188.174.0') and INET_ATON('192.188.174.255')
or INET_ATON(last_visit_ip) between INET_ATON('192.189.223.0') and INET_ATON('192.189.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('192.227.1.0') and INET_ATON('192.227.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('195.219.29.0') and INET_ATON('195.219.29.255')
or INET_ATON(last_visit_ip) between INET_ATON('195.219.75.0') and INET_ATON('195.219.75.255')
or INET_ATON(last_visit_ip) between INET_ATON('198.200.0.0') and INET_ATON('198.200.9.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.0.16.0') and INET_ATON('202.0.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.0.91.0') and INET_ATON('202.0.91.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.0.144.0') and INET_ATON('202.0.147.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.1.114.0') and INET_ATON('202.1.115.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.4.0.0') and INET_ATON('202.4.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.4.174.0') and INET_ATON('202.4.174.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.4.191.0') and INET_ATON('202.4.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.5.16.0') and INET_ATON('202.5.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.6.85.0') and INET_ATON('202.6.85.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.30.0') and INET_ATON('202.8.30.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.224.0') and INET_ATON('202.8.227.15')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.227.16') and INET_ATON('202.8.227.23')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.227.24') and INET_ATON('202.8.247.63')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.247.64') and INET_ATON('202.8.247.79')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.247.80') and INET_ATON('202.8.247.95')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.247.96') and INET_ATON('202.8.247.103')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.247.104') and INET_ATON('202.8.248.119')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.248.120') and INET_ATON('202.8.248.127')
or INET_ATON(last_visit_ip) between INET_ATON('202.8.248.128') and INET_ATON('202.8.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.14.85.0') and INET_ATON('202.14.87.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.37.74.0') and INET_ATON('202.37.74.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.40.172.0') and INET_ATON('202.40.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.41.147.0') and INET_ATON('202.41.147.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.44.100.0') and INET_ATON('202.44.103.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.47.125.0') and INET_ATON('202.47.125.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.47.129.0') and INET_ATON('202.47.129.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.47.132.0') and INET_ATON('202.47.133.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.47.140.0') and INET_ATON('202.47.141.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.52.34.0') and INET_ATON('202.52.34.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.52.54.0') and INET_ATON('202.52.55.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.52.160.0') and INET_ATON('202.52.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.57.32.0') and INET_ATON('202.57.43.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.57.44.0') and INET_ATON('202.57.44.31')
or INET_ATON(last_visit_ip) between INET_ATON('202.57.44.32') and INET_ATON('202.57.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.58.2.0') and INET_ATON('202.58.2.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.58.108.0') and INET_ATON('202.58.111.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.58.236.0') and INET_ATON('202.58.237.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.60.8.0') and INET_ATON('202.60.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.61.108.0') and INET_ATON('202.61.108.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.61.118.0') and INET_ATON('202.61.118.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.61.127.0') and INET_ATON('202.61.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.65.176.0') and INET_ATON('202.65.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.69.160.0') and INET_ATON('202.69.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.71.176.0') and INET_ATON('202.71.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.72.112.0') and INET_ATON('202.72.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.73.160.0') and INET_ATON('202.73.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.74.248.0') and INET_ATON('202.74.251.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.77.138.0') and INET_ATON('202.77.139.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.78.64.0') and INET_ATON('202.78.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.80.152.0') and INET_ATON('202.80.159.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.81.160.0') and INET_ATON('202.81.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.81.176.0') and INET_ATON('202.81.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.84.20.0') and INET_ATON('202.84.23.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.84.96.0') and INET_ATON('202.84.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.86.192.0') and INET_ATON('202.86.207.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.89.192.0') and INET_ATON('202.89.207.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.90.128.0') and INET_ATON('202.90.159.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.90.205.0') and INET_ATON('202.90.205.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.90.252.0') and INET_ATON('202.90.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.91.160.0') and INET_ATON('202.91.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.92.128.0') and INET_ATON('202.92.159.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.95.224.0') and INET_ATON('202.95.239.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.122.133.0') and INET_ATON('202.122.133.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.123.48.0') and INET_ATON('202.123.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.124.128.0') and INET_ATON('202.124.159.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.124.193.0') and INET_ATON('202.124.193.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.125.102.0') and INET_ATON('202.125.103.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.126.32.0') and INET_ATON('202.126.47.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.128.32.0') and INET_ATON('202.128.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.129.220.0') and INET_ATON('202.129.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.129.238.0') and INET_ATON('202.129.238.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.133.192.0') and INET_ATON('202.133.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.134.56.0') and INET_ATON('202.134.57.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.137.112.0') and INET_ATON('202.137.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.128.0') and INET_ATON('202.138.133.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.134.0') and INET_ATON('202.138.134.31')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.134.32') and INET_ATON('202.138.134.127')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.134.128') and INET_ATON('202.138.134.159')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.134.160') and INET_ATON('202.138.135.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.136.0') and INET_ATON('202.138.136.63')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.136.64') and INET_ATON('202.138.139.127')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.139.128') and INET_ATON('202.138.139.191')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.139.192') and INET_ATON('202.138.179.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.180.0') and INET_ATON('202.138.180.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.138.181.0') and INET_ATON('202.138.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.146.184.0') and INET_ATON('202.146.185.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.151.34.0') and INET_ATON('202.151.35.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.162.160.0') and INET_ATON('202.162.175.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.192.0') and INET_ATON('202.163.225.191')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.225.192') and INET_ATON('202.163.225.223')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.225.224') and INET_ATON('202.163.226.223')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.226.224') and INET_ATON('202.163.226.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.227.0') and INET_ATON('202.163.227.191')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.227.192') and INET_ATON('202.163.227.223')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.227.224') and INET_ATON('202.163.229.63')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.229.64') and INET_ATON('202.163.229.95')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.229.96') and INET_ATON('202.163.236.223')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.236.224') and INET_ATON('202.163.236.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.237.0') and INET_ATON('202.163.239.63')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.239.64') and INET_ATON('202.163.239.71')
or INET_ATON(last_visit_ip) between INET_ATON('202.163.239.72') and INET_ATON('202.163.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.164.160.0') and INET_ATON('202.164.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.165.60.0') and INET_ATON('202.165.61.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.166.188.0') and INET_ATON('202.166.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.173.4.0') and INET_ATON('202.173.4.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.173.7.0') and INET_ATON('202.173.7.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.175.192.0') and INET_ATON('202.175.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('202.183.32.0') and INET_ATON('202.183.47.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.12.18.0') and INET_ATON('203.12.18.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.17.249.0') and INET_ATON('203.17.249.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.33.196.0') and INET_ATON('203.33.196.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.34.246.0') and INET_ATON('203.34.246.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.55.67.0') and INET_ATON('203.55.67.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.56.241.0') and INET_ATON('203.56.241.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.76.192.0') and INET_ATON('203.76.214.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.76.215.0') and INET_ATON('203.76.215.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.76.216.0') and INET_ATON('203.76.240.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.76.241.0') and INET_ATON('203.76.241.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.76.242.0') and INET_ATON('203.76.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.82.32.0') and INET_ATON('203.82.47.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.84.160.0') and INET_ATON('203.84.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.87.128.0') and INET_ATON('203.87.229.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.87.230.0') and INET_ATON('203.87.230.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.87.231.0') and INET_ATON('203.87.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.92.8.0') and INET_ATON('203.92.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.99.232.0') and INET_ATON('203.99.239.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.104.64.0') and INET_ATON('203.104.95.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.111.224.0') and INET_ATON('203.111.239.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.114.64.0') and INET_ATON('203.114.95.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.114.236.0') and INET_ATON('203.114.239.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.115.128.0') and INET_ATON('203.115.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.118.244.0') and INET_ATON('203.118.247.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.119.4.0') and INET_ATON('203.119.7.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.129.4.0') and INET_ATON('203.129.5.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.131.64.0') and INET_ATON('203.131.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.131.128.0') and INET_ATON('203.131.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.147.104.0') and INET_ATON('203.147.107.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.153.6.0') and INET_ATON('203.153.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.158.8.0') and INET_ATON('203.158.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.160.130.0') and INET_ATON('203.160.131.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.160.160.0') and INET_ATON('203.160.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.161.176.0') and INET_ATON('203.161.177.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.161.188.0') and INET_ATON('203.161.188.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.0.0') and INET_ATON('203.167.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.64.0') and INET_ATON('203.167.81.79')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.81.80') and INET_ATON('203.167.81.95')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.81.96') and INET_ATON('203.167.108.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.109.0') and INET_ATON('203.167.109.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.110.0') and INET_ATON('203.167.118.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.119.0') and INET_ATON('203.167.119.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.167.120.0') and INET_ATON('203.167.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.169.0.0') and INET_ATON('203.169.3.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.170.20.0') and INET_ATON('203.170.23.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.171.6.0') and INET_ATON('203.171.7.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.172.0.0') and INET_ATON('203.172.23.95')
or INET_ATON(last_visit_ip) between INET_ATON('203.172.23.96') and INET_ATON('203.172.23.127')
or INET_ATON(last_visit_ip) between INET_ATON('203.172.23.128') and INET_ATON('203.172.24.95')
or INET_ATON(last_visit_ip) between INET_ATON('203.172.24.96') and INET_ATON('203.172.24.127')
or INET_ATON(last_visit_ip) between INET_ATON('203.172.24.128') and INET_ATON('203.172.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.174.0.0') and INET_ATON('203.174.3.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.175.0.0') and INET_ATON('203.175.3.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.175.124.0') and INET_ATON('203.175.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.176.116.0') and INET_ATON('203.176.119.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.177.0.0') and INET_ATON('203.177.28.39')
or INET_ATON(last_visit_ip) between INET_ATON('203.177.28.40') and INET_ATON('203.177.28.47')
or INET_ATON(last_visit_ip) between INET_ATON('203.177.28.48') and INET_ATON('203.177.33.63')
or INET_ATON(last_visit_ip) between INET_ATON('203.177.33.64') and INET_ATON('203.177.33.127')
or INET_ATON(last_visit_ip) between INET_ATON('203.177.33.128') and INET_ATON('203.177.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.189.8.0') and INET_ATON('203.189.15.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.190.218.0') and INET_ATON('203.190.221.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.191.134.0') and INET_ATON('203.191.135.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.201.180.0') and INET_ATON('203.201.180.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.208.22.0') and INET_ATON('203.208.22.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.208.24.0') and INET_ATON('203.208.31.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.213.192.0') and INET_ATON('203.213.223.255')
or INET_ATON(last_visit_ip) between INET_ATON('203.215.64.0') and INET_ATON('203.215.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('206.131.192.0') and INET_ATON('206.131.207.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.1.64.0') and INET_ATON('210.1.143.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.4.0.0') and INET_ATON('210.4.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.4.96.0') and INET_ATON('210.4.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.5.64.0') and INET_ATON('210.5.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.14.0.0') and INET_ATON('210.14.47.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.16.0.0') and INET_ATON('210.16.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.96.0') and INET_ATON('210.23.113.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.114.0') and INET_ATON('210.23.114.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.115.0') and INET_ATON('210.23.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.160.0') and INET_ATON('210.23.165.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.166.0') and INET_ATON('210.23.171.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.172.0') and INET_ATON('210.23.229.31')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.229.32') and INET_ATON('210.23.229.47')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.229.48') and INET_ATON('210.23.254.151')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.254.152') and INET_ATON('210.23.254.159')
or INET_ATON(last_visit_ip) between INET_ATON('210.23.254.160') and INET_ATON('210.23.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.185.160.0') and INET_ATON('210.185.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.213.64.0') and INET_ATON('210.213.214.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.213.215.0') and INET_ATON('210.213.215.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.213.216.0') and INET_ATON('210.213.241.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.213.242.0') and INET_ATON('210.213.246.255')
or INET_ATON(last_visit_ip) between INET_ATON('210.213.247.0') and INET_ATON('210.213.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('219.90.64.0') and INET_ATON('219.90.95.255')
or INET_ATON(last_visit_ip) between INET_ATON('221.121.96.0') and INET_ATON('221.121.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('221.121.184.0') and INET_ATON('221.121.191.255')
or INET_ATON(last_visit_ip) between INET_ATON('222.126.0.0') and INET_ATON('222.126.127.255')
or INET_ATON(last_visit_ip) between INET_ATON('222.127.0.0') and INET_ATON('222.127.255.255')
or INET_ATON(last_visit_ip) between INET_ATON('223.25.0.0') and INET_ATON('223.25.63.255')
or INET_ATON(last_visit_ip) between INET_ATON('223.130.8.0') and INET_ATON('223.130.11.255'))) a
join payment_29.pay_order o on a.uid = o.pay_user_id and o.get_payment = 1 and o.game_id = 79 join payment_29.pay_type pt on o.pay_type_id = pt.pay_type_id group by date,o.pay_type_id


页游查询全服等级大于等于200级的玩家的id,name,当前等级,最后登陆时间 --by zsl:
select 'S[value]',v.player_id,p.player_name,max(v.lev) as lev,from_unixtime(max(login_time)) as last_login_time 
from (select player_id,max(new_level) as lev from `1.[value]`.log_levelup  where new_level>=200 group by player_id having lev>=200) v 
left join `1.[value]`.log_create_player p on v.player_id=p.player_id
 left join `1.[value]`.log_login l on v.player_id=l.player_id 
group by v.player_id

德扑4月登录过的玩家数量 --by zsl:
select count(1) as num from 
(select player_id from `11.1`.log_login where login_time between unix_timestamp('2016-04-01') and unix_timestamp('2016-05-01')
 group by player_id) tab


风流三国5月份充值金额超过3000台币的订单号及玩家信息 --by zsl:
select order_sn,pay_user_id as uid,p.player_id,p.player_name,pay_amount,from_unixtime(pay_time) as pay_time,s.server_name from pay_order o 
left join server_list s on o.server_id=s.server_id 
left join `qiqiwu`.create_player p on p.uid=o.pay_user_id and p.server_id=s.server_internal_id and p.game_id=1
where o.game_id=1 and o.get_payment=1 and pay_time between unix_timestamp('2016-05-01') and unix_timestamp('2016-06-01') and pay_amount>=3000 order by pay_amount desc


学妹所有等级阶段、且所有VIP等级的玩家中，每一级购买体力玩家中的平均购买次数。（例如V1的1级的玩家均愿意购买几次体力，V1的2级玩家。。V1的3级玩家） ---by zsl:
select server_name,vip,lev,round(avg(times),1) as avg_times from
    (select ev.player_id,ev.lev,count(1) times,s.server_name,
    case 
    when sum(yuanbao_amount)<30 then 0
    when sum(yuanbao_amount)<300 then 1
    when sum(yuanbao_amount)<900  then 2
    when sum(yuanbao_amount)<1500  then 3
    when sum(yuanbao_amount)<3000  then 4
    when sum(yuanbao_amount)<6000  then 5
    when sum(yuanbao_amount)<9000  then 6
    when sum(yuanbao_amount)<15000  then 7
    when sum(yuanbao_amount)<20000  then 8
    when sum(yuanbao_amount)<30000  then 9
    when sum(yuanbao_amount)<45000  then 10
    when sum(yuanbao_amount)<60000  then 11
    when sum(yuanbao_amount)<100000  then 12
    when sum(yuanbao_amount)<250000  then 13
    when sum(yuanbao_amount)<500000  then 14
    else 15 end as vip
    from 
    (select e.id,e.player_id,e.created_at,max(v.lev) as lev,max(v.created_at) as lev_time from `66.34`.log_economy e 
    join `66.34`.log_levelup v on v.player_id=e.player_id and v.created_at<e.created_at and e.mid=97
    group by e.id) ev
    join `66.34`.log_create_player p on  p.player_id=ev.player_id
    join `payment`.pay_order o on o.pay_user_id=p.uid and o.game_id=66 and get_payment=1 and  o.pay_time<ev.lev_time
    join `payment`.server_list s on s.server_id=o.server_id and s.server_internal_id=34
    group by ev.id ) evo
where vip>0
group by vip,lev


学妹SX英雄平均抽取次数 ---by zsl:
select 'S74' as server_name,item_id,sum(num) as num,count(1) as day_num,round(sum(num)/count(1),2)  as avg_num from
(select item_id,from_unixtime(created_at,'%Y-%m-%d') as date,count(1) as num from 
(select id,created_at,
case 
when substring(item_ids,1,8) like '1%' then     substring(item_ids,1,8)
when substring(item_ids,10,8)  like '1%' then  substring(item_ids,10,8)
when substring(item_ids,19,8) like '1%' then   substring(item_ids,19,8)
when substring(item_ids,28,8) like '1%' then   substring(item_ids,28,8)
when substring(item_ids,37,8) like '1%' then   substring(item_ids,37,8)
when substring(item_ids,46,8) like '1%' then   substring(item_ids,46,8)
else 0 end as item_id
from `66.74`.log_summon 
   where summon_type=4 and  (item_ids like '1%' or item_ids like '%,1%')) s
where item_id>0
group by item_id,date) ss
group by item_id
order by avg_num desc

全服玩家每个等级阶段中，通过副本获得最多的装备和材料id是哪些。
目的：了解玩家每个等级阶段需求的装备或是材料是哪些，制作针对性的礼包。--by zsl:
select lev,table_id,count(1) as num from
(select t.table_id,max(v.lev) as lev from `66.1`.log_item t
join `66.1`.log_levelup v on v.player_id=t.player_id and v.created_at<t.created_at
where mid in(114,117) and num>0 
group by t.id) tv
group by lev,table_id
order by lev,num desc

风流三国时间段内消耗元宝超过2000的玩家 -- by zsl:
select s.server_track_name as server_name,player_id,abs(sum(diff_yuanbao)) as yuanbao_spend from `1.210`.log_economy e 
left join server_list s on s.server_internal_id=e.server_id and s.game_id=1
where diff_yuanbao<0 and action_time between unix_timestamp('2016-06-01') and unix_timestamp('2016-06-10')
group by player_id
having yuanbao_spend>=2000
order by yuanbao_spend desc

台湾三国战斗力大于7000000的玩家(游戏后台数据，需要在游戏服务器数据功能里查询)：
SELECT U.OperatorID, U.ServerID, P.PlayerID, P.Name, P.Attack From Player AS P LEFT JOIN User_Player AS U ON U.PlayerID = P.PlayerID WHERE Attack >= 7000000
