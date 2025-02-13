USE [Medicarehsw]
GO

/****** Object:  Table [dbo].[BerechtigungAnwender]    Script Date: 12.02.2025 14:25:51 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[BerechtigungAnwender](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Anwender] [nvarchar](100) NULL,
	[Berechtigung-ID] [int] NULL,
	[Form] [nvarchar](100) NULL,
	[Menu] [nvarchar](100) NULL,
	[Berechtigt] [nvarchar](10) NULL,
	[Mitarbeiter] [nvarchar](100) NULL,
	[Vorlage] [int] NULL,
	[Changed] [smalldatetime] NULL,
	[User] [nvarchar](50) NULL,
	[gelöscht] [int] NULL,
	[gelöschtDatum] [smalldatetime] NULL,
	[gelöschtUser] [nvarchar](50) NULL,
	[Kennwort] [nvarchar](50) NULL,
	[Administrator] [int] NULL,
	[deaktiviert] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 80, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

