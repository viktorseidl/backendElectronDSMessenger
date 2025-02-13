USE [Medicarehsw]
GO

/****** Object:  Table [dbo].[EMail]    Script Date: 12.02.2025 14:24:43 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[EMail](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Datum] [datetime] NULL,
	[Grund_ID] [int] NOT NULL,
	[Betreff] [nchar](50) NULL,
	[Nachricht] [ntext] NULL,
	[Sender] [nchar](50) NOT NULL,
	[Empfänger] [nchar](50) NOT NULL,
	[Erledigt] [bit] NULL,
	[Wichtig] [nchar](1) NULL,
	[Anhang] [int] NULL,
	[gelöscht] [int] NULL,
	[gelöschtDatum] [smalldatetime] NULL,
	[gelöschtUser] [nvarchar](50) NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

